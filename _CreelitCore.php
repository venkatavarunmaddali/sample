<?php

namespace Cit\MLS;

use Cit\Helper\Property\PropertyImageHelper;
use SimpleXMLElement;

/**
 * MLS Integration: KvCore Generic
 *
 * Server: KvCore
 *
 * Base URL: https://api.kvcore.com/amarki
 * Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0eXBlIjoiYW1hcmtpIiwiZGF0ZSI6IjIwMzAtMDEtMDEgMTI6MDA6MDAifQ.7Wgk0thPddY3bZFkPNv4IkWMVqcUYPvS69Z4f72cZvg
 */

class _CreelitCore extends _Generic
{
    use Mapping\_CreelitCore;

    /**
     * url version for agent request
     *
     * as from docs - timestamp not supported
     */
    const REQUEST_AGENT_VERSION = '4';

    /**
     * url version for property request
     */
    const REQUEST_PROPERTY_VERSION = '5';

    /**
     * kvcore bearer auth
     *
     * @var string
     */
    private $vendorAuth;

    /**
     * kvcore client key
     *
     * @var string
     */
    private $clientKey;

    public function parseFull()
    {
        $updated = 0;

        $retsProperties = $this->requestProperties();

        /** @var Record $item */
        foreach ($retsProperties['Listing'] as $retsProperty) {
            $amarkiId = $this->ph->mlsAmarkiId(static::LIST_ID, $this->getMlsIdProperty($retsProperty));

            if ($amarkiId) {
                $syncMeta = $this->mlh->getPropertySyncMeta($amarkiId);
                $amarkiId = $this->updateProperty($amarkiId, $retsProperty, $syncMeta);
            } else {
                $amarkiId = $this->addProperty($retsProperty);
            }

            if ($amarkiId) {
                $updated++;
            }
        }

        $this->mlh->updateSyncMeta(static::LIST_ID, static::LIST_VERSION, $updated);
    }

    /**
     * parse kvcore agents into amarki
     *
     * one-time-run atm
     *
     * @return int added/updated records count
     */
    public function parseFullAgents()
    {
        $updated = 0;

        $retsAgents = $this->requestAgents();

        /** @var array $retsAgent */
        foreach ($retsAgents as $retsAgent) {
            $amarkiId = $this->uh->mlsAmarkiIdAgent(static::LIST_ID, $this->getMlsIdAgent($retsAgent));

            $companyId = MLS::listToCompany(static::LIST_ID);
            if (!$companyId) {
                continue;
            }

            // try find an agent by email
            if (!$amarkiId && $companyId) {
                $amarkiId = $this->uh->mlsAgentIdByEmail($retsAgent['Email'], $companyId);

                //if found - add relation
                if ($amarkiId) {
                    $this->mlh->addAgentSyncMeta(static::LIST_ID, $this->getMlsIdAgent($retsAgent), $amarkiId, static::LIST_ID);
                }
            }

            if ($amarkiId) {
                $amarkiId = $this->updateAgent($amarkiId, $retsAgent);
            } else {
                $amarkiId = $this->addAgent($companyId, $retsAgent);
            }

            if ($amarkiId) {
                $updated++;
            }
        }

        return $updated;
    }

    /**
     * parse kvcore crm contacts into amarki
     *
     * one-time-run atm
     *
     * @return int added/updated contacts count
     */
    public function parseFullCrm()
    {
        $updated = 0;

        $mlsAgentIds = $this->mlh->getMlsAgentIds(static::LIST_ID);

        foreach ($mlsAgentIds as $mlsAgentId) {
            $retsLists = $this->requestCrmLists($mlsAgentId);

            /** @var array $retsList */
            foreach ($retsLists as $retsList) {
                $mlsIdCrmList = $this->getMlsIdCrmList($retsList);

                $amarkiListId = $this->ch->mlsAmarkiIdList(static::LIST_ID, $mlsIdCrmList);

                if ($amarkiListId) {
                    $amarkiListId = $this->updateCrmList($amarkiListId, $mlsAgentId, $retsList);
                } else {
                    $amarkiListId = $this->addCrmList($mlsAgentId, $retsList);
                }

                if (!$amarkiListId) {
                    continue;
                }

                $retsContacts = $this->requestCrmContacts($mlsAgentId, $mlsIdCrmList);

                /** @var array $retsContact */
                foreach ($retsContacts as $retsContact) {
                    // check if we need to skip contact because of no email
                    if (($retsContact['email'] == '') || ($retsContact['email'] == 'noemail@kvcore.com')) {
                        continue;
                    }

                    $amarkiContactId = $this->ch->mlsAmarkiIdContact(
                        static::LIST_ID,
                        $this->getMlsIdCrmContact($retsContact)
                    );

                    if ($amarkiContactId) {
                        $amarkiContactId = $this->updateCrmContact($mlsIdCrmList, $amarkiContactId, $retsContact);
                    } else {
                        $amarkiContactId = $this->addCrmContact($mlsIdCrmList, $retsContact);
                    }

                    if ($amarkiContactId) {
                        $updated++;
                    }
                }

            }

        }

        return $updated;
    }

    protected function initApiInterface($mlsData)
    {
        list($vendorAuth, $clientKey) = explode('|', $mlsData['mls_list_creds']);

        $this->vendorAuth = $vendorAuth;
        $this->clientKey = $clientKey;
    }

    protected function syncPhoto($amarkiId, $retsProperty)
    {

        $images = is_array($retsProperty['Pictures']['Picture']) ? $retsProperty['Pictures']['Picture'] : [];

        // for now, no image data means no sync activities
        if (!$images) {
            return;
        }

        $images_url = [];

        foreach ($images as $key => $value) {
            array_push($images_url, $value['PictureUrl']);
        }

        /** @var PropertyImageHelper $pih */
        $pih = PropertyImageHelper::getInstance();

        $pih->removeExternalMls($amarkiId);
        $pih->addMlsExternalMultiple($amarkiId, $images_url);
    }

    /**
     * perform get request
     *
     * @param string $url
     *
     * @return string
     */
    private function apiRequestGet($url)
    {
        $ch = curl_init($url);

        //ssl parameters - disable ssl checks to allow self-signed certs
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->vendorAuth
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }

    /**
     * perform properties request and parse xml to array
     *
     * @return mixed
     */
    private function requestProperties()
    {
        $url = $this->baseUrl;
        return $this->propertyXmlToArray($url);
    }

    /**
     * perform agents request and parse xml to array
     *
     * @return mixed
     */
    private function requestAgents()
    {
        $url = $this->baseUrl;

        $url .= '/export/agents/';
        $url .= $this->clientKey;
        $url .= '/' . static::REQUEST_AGENT_VERSION;

        $raw = $this->apiRequestGet($url);
        return $this->agentXMlToArray($raw);
    }

    /**
     * perform crm lists request and parse json to array
     *
     * @param int $mlsAgentId
     *
     * @return array
     */
    private function requestCrmLists($mlsAgentId)
    {
        $url = $this->baseUrl;

        $url .= '/users/';
        $url .= $this->clientKey;
        $url .= "/{$mlsAgentId}/hashtags";

        $raw = $this->apiRequestGet($url);

        if ($raw == '[]') {
            return [];
        }

        $parsed = @json_decode($raw, true);

        if (!$parsed) {
            return [];
        }

        return $parsed;
    }

    /**
     * perform crm contacts request and parse json to array
     *
     * @param int $mlsAgentId
     * @param int $mlsIdCrmList
     *
     * @return array
     */
    private function requestCrmContacts($mlsAgentId, $mlsIdCrmList)
    {
        $url = $this->baseUrl;

        $url .= '/users/';
        $url .= $this->clientKey;
        $url .= "/{$mlsAgentId}/contacts?hashtags[]={$mlsIdCrmList}";

        $raw = $this->apiRequestGet($url);

        if ($raw == '[]') {
            return [];
        }

        $parsed = @json_decode($raw, true);

        if (!$parsed || !isset($parsed['leads'])) {
            return [];
        }

        return $parsed['leads'];
    }

    /**
     * convert property response to array of property data
     *
     * @param string $raw
     *
     * @return array
     */
    private function propertyXmlToArray($raw)
    {

        $listing_array = json_decode(json_encode(simplexml_load_file($raw)),true);

        return $listing_array;
    }

    /**
     * convert agent response to array of property data
     *
     * @param string $raw
     *
     * @return array
     */
    private function agentXmlToArray($raw)
    {
        $res = [];

        $xml = new SimpleXMLElement($raw);

        foreach ($xml->Agent as $rawAgent) {
            $agent = [];
            $rawAgent = (array)$rawAgent;

            foreach ($rawAgent as $key => $rawValue) {
                switch ($key) {
                    case 'Phone':
                        $agent['Phone'] = (string)$rawValue[0]->PhoneNumber;
                        $agent['Phone'] = ($agent['Phone']) ? $agent['Phone'] : null;
                        break;
                    default:
                        $value = (string)$rawValue;
                        $agent[$key] = ($value) ? $value : null;
                }
            }

            $res[] = $agent;
        }

        return $res;
    }
}
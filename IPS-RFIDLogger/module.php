<?php

require_once __DIR__ . '/../libs/TasmotaService.php';

class IPS_RFIDLogger extends IPSModule
{

    public function Create()
    {
        //Never delete this line!
        parent::Create();
        //Connect to Websocket Client
        $this->ConnectParent('{3AB77A94-3467-4E66-8A73-840B4AD89582}');
        $this->RegisterPropertyString('Transponder', '[]');
        $this->RegisterVariableString('ESPRFID_LastTransponder','Last Transponder',"");
        $this->createVariablenProfiles();
   }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();
        $this->ConnectParent('{3AB77A94-3467-4E66-8A73-840B4AD89582}');

    }

    public function ReceiveData($JSONString)
    {
        $this->SendDebug('JSON', $JSONString, 0);
        $Data = json_decode($JSONString);
        $Buffer = utf8_decode($Data->Buffer);
        $this->SendDebug('Buffer', $Buffer, 0);
        $Buffer = json_decode($Buffer);
        SetValue($this->GetIDForIdent('ESPRFID_LastTransponder'), $Buffer->uid);

        //Get all Transponder with access
        $TransponderList = $this->ReadPropertyString('Transponder');
        $TransponderList = json_decode($TransponderList);
        $TransponderIDs = array_column($TransponderList, "TransponderID");
        //Check if the scanned transponder has access and send relay command
        if (in_array($Buffer->uid,$TransponderIDs)) {
            $this->SendDebug("Access scanned Transponder",$Buffer->uid,0);
            $this->setRelais();
        }
    }

    private function createVariablenProfiles()
    {
        //Online / Offline Profile
        $this->RegisterProfileBooleanEx('RFIDLogger.TransponderStatus', 'Information', '', '', array(
            array(false, 'Inactive',  '', 0xFF0000),
            array(true, 'Active',  '', 0x00FF00)
        ));
    }

    public function setRelais() {
        $array["command"] ="testrelay";
        $data = (json_encode($array));
        $SendData = json_encode(Array("DataID" => "{79827379-F36E-4ADA-8A95-5F8D1DC92FA9}", "Buffer" => $data));
        $this->SendDataToParent($SendData);
    }

    public function AddLastTransponder() {
        $TransponderList = $this->ReadPropertyString('Transponder');
        $this->SendDebug("Actually TransponderList", $TransponderList,0);

        $LastScannedTransponder = GetValue($this->GetIDForIdent('ESPRFID_LastTransponder'));
        //Json List to array
        $TransponderList = json_decode($TransponderList);
        $TransponderIDs = array_column($TransponderList, "TransponderID");
        if (!in_array($LastScannedTransponder,$TransponderIDs)) {
            //Add Last Transponder to an array for List
            $Transponder = array(
                'TransponderID'   => $LastScannedTransponder,
                'TransponderName'   => "");

            $TransponderList[count($TransponderList)] = $Transponder;

            //List array to Json for Configurationform
            $TransponderList = json_encode($TransponderList);
            $this->SendDebug("New TransponderList", $TransponderList,0);


            IPS_SetProperty($this->InstanceID, "Transponder",$TransponderList);
            IPS_ApplyChanges($this->InstanceID);
         } else {
            IPS_LogMessage("RFIDLogger", "The transponder is already in the list");
        }
    }

    protected function RegisterProfileBoolean($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, $StepSize)
    {
        if (!IPS_VariableProfileExists($Name)) {
            IPS_CreateVariableProfile($Name, 0);
        } else {
            $profile = IPS_GetVariableProfile($Name);
            if ($profile['ProfileType'] != 0) {
                throw new Exception('Variable profile type does not match for profile ' . $Name);
            }
        }
        IPS_SetVariableProfileIcon($Name, $Icon);
        IPS_SetVariableProfileText($Name, $Prefix, $Suffix);
        IPS_SetVariableProfileValues($Name, $MinValue, $MaxValue, $StepSize);
    }
    protected function RegisterProfileBooleanEx($Name, $Icon, $Prefix, $Suffix, $Associations)
    {
        if (count($Associations) === 0) {
            $MinValue = 0;
            $MaxValue = 0;
        } else {
            $MinValue = $Associations[0][0];
            $MaxValue = $Associations[count($Associations) - 1][0];
        }
        $this->RegisterProfileBoolean($Name, $Icon, $Prefix, $Suffix, $MinValue, $MaxValue, 0);
        foreach ($Associations as $Association) {
            IPS_SetVariableProfileAssociation($Name, $Association[0], $Association[1], $Association[2], $Association[3]);
        }
    }

}

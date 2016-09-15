<?php
namespace PSN;

class Messaging
{
    private $oauth;
    private $refresh_token;

    public function __construct($tokens)
    {
        $this->oauth = $tokens["oauth"];
        $this->refresh_token = $tokens["refresh"];
    }
    public function Get($MessageGroupID)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: application/json; charset=utf-8'
        );
        $response = \Utilities::SendRequest(MESSAGE_URL . '/' . $MessageGroupID . '/messages?fields=@default,messageGroup,stickerDetail,thumbnailDetail,body,takedownDetail,eventDetail,partyDetail&npLanguage=en-GB', $headers, false, null, "GET", null);


        $data = json_decode($response['body'], false);

        return $data;       
    }
    public function GetAll()
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: application/json; charset=utf-8'
        );
        $tokens = array(
            "oauth" => $this->oauth,
            "refresh" => $this->refresh_token
        );
        $_user = new \PSN\User($tokens);
        $_onlineId = $_user->Me()->profile->onlineId;

        $response = \Utilities::SendRequest(MESSAGE_USERS_URL . $_onlineId ."/messageGroups?fields=@default,messageGroupDetail,totalUnseenMessages,totalMessages,myGroupDetail,newMessageDetail,latestMessage,takedownDetail&includeEmptyMessageGroup=true", $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);

        return $data;    
    }
    public function GetFavorites()
    {        
        // Favorites Filter
        $MessageGroups = $this->GetAll();
        $MessageGroupsFavorite = array();
        for ($i = 0; $i < count($MessageGroups->messageGroups); $i++)
        {
            // Check Favorite Group Status
            $FavoriteStatus = (int)$MessageGroups->messageGroups[$i]->messageGroupDetail->myGroupDetail->myGroupFlag;
            if ($FavoriteStatus == 1)
            {
                $MessageGroupsFavorite[] = $MessageGroups->messageGroups[$i];
            }
        }
        
        // Update Data
        $MessageGroups->messageGroups = $MessageGroupsFavorite;
        $MessageGroups->size = count($MessageGroupsFavorite);
        $MessageGroups->totalResults = count($MessageGroupsFavorite);
        
        // Return Favorites
        return $MessageGroups;
    }
    
    public function GetAudioAttachment($MessageGroupID, $MessageUid)
    {
        return $this->GetAttachment($MessageGroupID, $MessageUid, "voice-data-0", "audio/mpeg"); 
    }
    public function GetImageAttachment($MessageGroupID, $MessageUid)
    {
        return $this->GetAttachment($MessageGroupID, $MessageUid, "image-data-0", "image/jpeg");
    }
    public function GetAttachment($MessageGroupID, $MessageUid, $ContentKey, $ContentType)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: ' . $ContentType,
            'Content-Transfer-Encoding: binary'
        );
        $response = \Utilities::SendRequest(MESSAGE_URL . '/' . $MessageGroupID . '/messages/' . $MessageUid . '?contentKey=' . $ContentKey, $headers, false, null, "GET", null);


        $data = base64_encode($response['body']);

        return $data; 
    }
    public function Remove($MessageGroupID)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: application/json; charset=utf-8'
        );

        $tokens = array(
            "oauth" => $this->oauth,
            "refresh" => $this->refresh_token
        );
        
        $_user = new \PSN\User($tokens);
        $_onlineId = $_user->Me()->profile->onlineId;

        $response = \Utilities::SendRequest(MESSAGE_URL . "/" . $MessageGroupID . "/users/" . $_onlineId, $headers, false, null, "DELETE", null);

        $data = json_decode($response['body'], false);

        return $data;
    }
    
    // Read Messages
    public function Read($MessageGroupID, $MessageIDList)
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: application/json; charset=utf-8'
        );

        \Utilities::SendRequest(MESSAGE_URL . "/" . $MessageGroupID . "/messages?messageUid=" . implode (",", $MessageIDList), $headers, false, null, "PUT", json_encode(array("seenFlag" => true)));
    }

    // Send Audio Messages
    public function AudioMessage($PSN, $Audio, $AudioLength)
    {
        return $this->Message($PSN, "", $Audio, 1011, $AudioLength);
    }
    public function AudioMessageGroup($GroupID, $Audio, $AudioLength)
    {
        return $this->MessageGroup($GroupID, "", $Audio, 1011, $AudioLength);
    }
    
    // Send Image Messages
    public function ImageMessage($PSN, $Image, $Message = "")
    {
        return $this->Message($PSN, $Message, $Image, 3);
    }
    public function ImageMessageGroup($GroupID, $Image, $Message = "")
    {
        return $this->MessageGroup($GroupID, $Message, $Image, 3);
    }

    // Send Text Messages
    public function TextMessage($PSN, $Message)
    {
        return $this->Message($PSN, $Message, "", 1);
    }
    public function TextMessageGroup($GroupID, $Message)
    {
        return $this->MessageGroup($GroupID, $Message, "", 1);
    }
    
    // Send Message to New Group
    private function Message($PSN, $MessageText = "", $Attachment = "", $MessageType = 1, $AudioLength = "")
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: multipart/mixed; boundary="gc0p4Jq0M2Yt08jU534c0p"',
        );
        $json_body = array(
            "to" => array(),
            "message" => array(
                "fakeMessageUid" => 1234,
                "body" => $MessageText,
                "messageKind" => $MessageType
            )
        );
        
        // Handle List of PSN Names
        if (is_array($PSN))
        {
            foreach ($PSN as $Name)
            {
                $json_body["to"][] = $Name;
            }
        }
        else
        {
            $json_body["to"][] = $PSN;
        }
        
        // Get Attachment Content and Length
        if (file_exists($Attachment) || filter_var($Attachment, FILTER_VALIDATE_URL) == true) {
            $AttachmentContent = file_get_contents($Attachment);
            $AttachmentLength  = strlen($AttachmentContent);
        } else {
            $AttachmentContent = $Attachment;
            $AttachmentLength  = strlen($AttachmentContent);
        }
        
        // Handle Attachment Types
        if ($MessageType == 1011)
        {
            // Audio
            $ContentKey = "voice-data-0";
            $ContentType = "audio/3gpp";
        }
        else if ($MessageType == 3)
        {
            // Image
            $ContentKey = "image-data-0";
            $ContentType = "image/jpeg";
        }
        
        // Build Message With or Without Content
        if ($Attachment)
        {
            $message = "\n";
            $message .= "--gc0p4Jq0M2Yt08jU534c0p\n";
            $message .= "Content-Type: application/json; charset=utf-8\n";
            $message .= "Content-Description: message\n";
            $message .= "\n";
            
            $message .= json_encode($json_body) . "\n";
            
            $message .= "--gc0p4Jq0M2Yt08jU534c0p\n";
            $message .= "Content-Type: $ContentType\n";
            $message .= "Content-Disposition: attachment\n";
            $message .= "Content-Description: $ContentKey\n";
            $message .= "Content-Transfer-Encoding: binary\n";
            $message .= "Content-Length: $AttachmentLength\n";
            if ($MessageType == 1011)
            {
                $message .= "Content-Voice-Data-Playback-Time: $AudioLength\n";
            }
            $message .= "\n";
            
            $message .= $AttachmentContent . "\n";
            
            $message .= "--gc0p4Jq0M2Yt08jU534c0p--\n\n";
        }
        else
        {
            $message = "\n";
            $message .= "--gc0p4Jq0M2Yt08jU534c0p\n";
            $message .= "Content-Type: application/json; charset=utf-8\n";
            $message .= "Content-Description: message\n";
            $message .= "\n";
            
            $message .= json_encode($json_body) . "\n";
            
            $message .= "--gc0p4Jq0M2Yt08jU534c0p--\n\n";
        }
        
        $response = \Utilities::SendRequest(MESSAGE_URL, $headers, false, null, "POST", $message);

        $data = json_decode($response['body'], false);
        
        return $data;
    }
    
    // Send Message to Existing Group
    public function MessageGroup($GroupID, $MessageText = "", $Attachment = "", $MessageType = 1, $AudioLength = "")
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: multipart/mixed; boundary="gc0p4Jq0M2Yt08jU534c0p"',
        );
        $json_body = array(
            "message" => array(
                "fakeMessageUid" => 1234,
                "body" => $MessageText,
                "messageKind" => $MessageType
            )
        );
        
        // Get Attachment Content and Length
        if (file_exists($Attachment) || filter_var($Attachment, FILTER_VALIDATE_URL) == true) {
            $AttachmentContent = file_get_contents($Attachment);
            $AttachmentLength  = strlen($AttachmentContent);
        } else {
            $AttachmentContent = $Attachment;
            $AttachmentLength  = strlen($AttachmentContent);
        }
        
        // Handle Attachment Types
        if ($MessageType == 1011)
        {
            // Audio
            $ContentKey = "voice-data-0";
            $ContentType = "audio/3gpp";
        }
        else if ($MessageType == 3)
        {
            // Image
            $ContentKey = "image-data-0";
            $ContentType = "image/jpeg";
        }
        
        // Build Message With or Without Content
        if ($Attachment)
        {
            $message = "\n";
            $message .= "--gc0p4Jq0M2Yt08jU534c0p\n";
            $message .= "Content-Type: application/json; charset=utf-8\n";
            $message .= "Content-Description: message\n";
            $message .= "\n";
            
            $message .= json_encode($json_body) . "\n";
            
            $message .= "--gc0p4Jq0M2Yt08jU534c0p\n";
            $message .= "Content-Type: $ContentType\n";
            $message .= "Content-Disposition: attachment\n";
            $message .= "Content-Description: $ContentKey\n";
            $message .= "Content-Transfer-Encoding: binary\n";
            $message .= "Content-Length: $AttachmentLength\n";
            if ($MessageType == 1011)
            {
                $message .= "Content-Voice-Data-Playback-Time: $AudioLength\n";
            }
            $message .= "\n";
            
            $message .= $AttachmentContent . "\n";
            
            $message .= "--gc0p4Jq0M2Yt08jU534c0p--\n\n";
        }
        else
        {
            $message = "\n";
            $message .= "--gc0p4Jq0M2Yt08jU534c0p\n";
            $message .= "Content-Type: application/json; charset=utf-8\n";
            $message .= "Content-Description: message\n";
            $message .= "\n";
            
            $message .= json_encode($json_body) . "\n";
            
            $message .= "--gc0p4Jq0M2Yt08jU534c0p--\n\n";
        }
        
        $response = \Utilities::SendRequest(MESSAGE_URL . '/' . $GroupID . '/messages', $headers, false, null, "POST", $message);

        $data = json_decode($response['body'], false);
        
        return $data;
    }
    
    // Add Users to Group
    public function GroupAddUsers($MessageGroupID, $PSN)
    {
        // Set Headers
        $headers = array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Bearer ' . $this->oauth
        );
        
        // Handle List of PSN Names
        $Names = array();
        if (is_array($PSN))
        {
            foreach ($PSN as $Name)
            {
                $Names[] = $Name;
            }
        }
        else
        {
            $Names[] = $PSN;
        }
            
        // Send Request
        \Utilities::SendRequest(MESSAGE_URL . '/' . $MessageGroupID . '/users', $headers, false, null, "POST", json_encode(array("members" => $Names)));
    }
    
    // Favorite Group
    public function FavoriteGroup($MessageGroupID)
    {
        $this->SetFavoriteGroupStatus($MessageGroupID, true);
    }
    // Unfavorite Group
    public function UnfavoriteGroup($MessageGroupID)
    {
        $this->SetFavoriteGroupStatus($MessageGroupID, false);
    }
    private function SetFavoriteGroupStatus($MessageGroupID, $Flag)
    {
        // Get PSN Name
        $tokens = array(
            'oauth' => $this->oauth,
            'refresh' => $this->refresh_token
        );
        $_user = new \PSN\User($tokens);
        $_onlineId = $_user->Me()->profile->onlineId;
        
        // Set Headers
        $headers = array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Bearer ' . $this->oauth
        );
        
        // Send Request
        \Utilities::SendRequest(MESSAGE_USERS_URL . $_onlineId . "/messageGroups/" . $MessageGroupID . "/myGroup", $headers, false, null, "PUT", json_encode(array("myGroupFlag" => $Flag))); 
    }
    
    // Group Image
    public function SetGroupImage($MessageGroupID, $Image)
    {
        // Set Headers
        $headers = array(
            'Content-Type: image/png',
            'Content-Description: image-data-0',
            'Authorization: Bearer ' . $this->oauth
        );
        
        // Get Attachment Content and Length
        if (file_exists($Image) || filter_var($Image, FILTER_VALIDATE_URL) == true) {
            $ImageContent = file_get_contents($Image);
            $ImageLength  = strlen($ImageContent);
        } else {
            $ImageContent = $Image;
            $ImageLength  = strlen($AttachmentContent);
        }
        
        // Send Request
        \Utilities::SendRequest(MESSAGE_URL . "/" . $MessageGroupID . "/thumbnail", $headers, false, null, "PUT", $ImageContent); 
    }

    // Group Name
    public function SetGroupName($MessageGroupID, $Name)
    {
        // Set Headers
        $headers = array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Bearer ' . $this->oauth
        );
        
        // Send Request
        \Utilities::SendRequest(MESSAGE_URL . "/" . $MessageGroupID . "/name", $headers, false, null, "PUT", json_encode(array("messageGroupName" => $Name))); 
    }
}

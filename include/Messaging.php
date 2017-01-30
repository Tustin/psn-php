<?php
namespace PSN;

class Messaging
{
    private $oauth;
    private $refresh_token;
    private $me;
    
    public function __construct($tokens)
    {
        $this->oauth = $tokens["oauth"];
        $this->refresh_token = $tokens["refresh"];
        $this->me = new \PSN\User(array("oauth" => $this->oauth, "refresh" => $this->refresh_token));
        $this->me = $this->me->Me()->profile->onlineId;
    }
        
    // Get All Groups
    public function GetAll()
    {
        $headers = array
        (
            'Authorization: Bearer ' . $this->oauth
        );

        $url = MESSAGE_THREADS_URL . "/?fields=threadMembers,threadNameDetail,threadThumbnailDetail,threadProperty,latestMessageEventDetail,latestTakedownEventDetail,newArrivalEventDetail&limit=200&offset=0";
        $response = \Utilities::SendRequest($url, $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);

        return $data;    
    }
    // Get Favorite Groups
    public function GetFavorites()
    {
        $headers = array
        (
            'Authorization: Bearer ' . $this->oauth
        );

        $url = MESSAGE_THREADS_URL . "/?fields=threadMembers,threadNameDetail,threadThumbnailDetail,threadProperty,latestMessageEventDetail,latestTakedownEventDetail,newArrivalEventDetail&limit=200&offset=0&filter=favorite";
        $response = \Utilities::SendRequest($url, $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);

        return $data;
    }
    // Delete Group
    public function Remove($MessageGroupID)
    {
        $headers = array
        (
            'Authorization: Bearer ' . $this->oauth,
        );

        $url = MESSAGE_THREADS_URL . "/" . $MessageGroupID . "/users/me";
        $response = \Utilities::SendRequest($url, $headers, false, null, "DELETE", null);

        $data = json_decode($response['body'], false);

        return $data;
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
        // Set Headers
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->oauth
        );
        
        // Send Request
        \Utilities::SendRequest(MESSAGE_USERS_URL  . "me/threads/" . $MessageGroupID . "/favorites", $headers, false, null, "PUT", json_encode(array("favoriteDetail" => array("favoriteFlag" => $Flag)))); 
    }
    
    // Add Users to Group
    public function GroupAddUsers($MessageGroupID, $PSN)
    {
        // Set Headers
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->oauth
        );
        
        // Handle List of PSN Names
        if (is_array($PSN))
        {
            foreach ($PSN as $Name)
            {
                $json_body["userActionEventDetail"]["targetList"][] = array("onlineId" => $Name);
            }
        }
        else
        {
            $json_body["userActionEventDetail"]["targetList"][] = array("onlineId" => $PSN);
        }
           
        // Send Request        
        $response = \Utilities::SendRequest(MESSAGE_THREADS_URL . '/' . $MessageGroupID . '/users', $headers, false, null, "POST", json_encode($json_body));
        
        $data = json_decode($response['body'], false);
        
        return $data;
    }
    
    // Add Group Image
    public function AddGroupImage($MessageGroupID, $Image)
    {        
        // Set Headers
        $headers = array(
            'Content-Type: multipart/form-data; boundary="gc0p4Jq0M2Yt08jU534c0p"',
            'Authorization: Bearer ' . $this->oauth
        );
        
        // Get Attachment Content and Length
        if (file_exists($Image) || filter_var($Image, FILTER_VALIDATE_URL) == true) {
            $ImageContent = file_get_contents($Image);
            $ImageLength  = strlen($ImageContent);
        } else {
            $ImageContent = $Image;
            $ImageLength  = strlen($ImageContent);
        }
                
        $message = "\n";
        $message .= "--gc0p4Jq0M2Yt08jU534c0p\n";
        $message .= "Content-Type: image/jpeg\n";
        $message .= "Content-Disposition: form-data; name=\"threadThumbnail\"\n";
        $message .= "Content-Transfer-Encoding: binary\n";
        $message .= "Content-Length: " . $ImageLength . "\n";
        $message .= "\n";
        $message .= $ImageContent . "\n";
        $message .= "--gc0p4Jq0M2Yt08jU534c0p--\n\n";
        
        \Utilities::SendRequest(MESSAGE_THREADS_URL . "/" . $MessageGroupID . "/thumbnail", $headers, false, null, "PUT", $message); 
    }
    
    // Remove Group Image
    public function RemoveGroupImage($MessageGroupID, $Image)
    {
        // Set Headers
        $headers = array(
            'Authorization: Bearer ' . $this->oauth
        );
        
        // Send Request
        \Utilities::SendRequest(MESSAGE_THREADS_URL . "/" . $MessageGroupID . "/thumbnail", $headers, false, null, "DELETE", null); 
    }
    
    // Set Group Name
    public function SetGroupName($MessageGroupID, $Name)
    {
        // Set Headers
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->oauth
        );

        // Send Request
        \Utilities::SendRequest(MESSAGE_THREADS_URL . "/" . $MessageGroupID . "/name", $headers, false, null, "PUT", json_encode(array("threadNameDetail" => array("threadName" => $Name)))); 
    }
    
    // Get Messages
    public function Get($MessageGroupID)
    {
        $headers = array
        (
            'Authorization: Bearer ' . $this->oauth
        );

        $url = MESSAGE_THREADS_URL . '/' . $MessageGroupID . '?count=200&fields=threadMembers,threadNameDetail,threadThumbnailDetail,threadProperty,latestTakedownEventDetail,newArrivalEventDetail,threadEvents';
        $response = \Utilities::SendRequest($url, $headers, false, null, "GET", null);

        $data = json_decode($response['body'], false);
                        
        return $data;       
    }
    // Get Messages Attachments
    public function GetAudioAttachment($URL)
    {
        return $this->GetAttachment($URL, "audio/3gpp"); 
    }
    public function GetImageAttachment($URL)
    {
        return $this->GetAttachment($URL, "image/png");
    }
    public function GetAttachment($URL, $ContentType)
    {
        $headers = array
        (
            'Authorization: Bearer ' . $this->oauth
        );
        
        $response = \Utilities::SendRequest($URL, $headers, false, null, "GET", null);

        $data = base64_encode($response['body']);

        return $data; 
    }
    
    // Read Messages
    public function Read($MessageGroupID, $LastMessageID)
    {
        // Set Headers
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->oauth
        );
        
        // TODO: Avoid Hardcode JSON
        $message = 
        '{
          "threads" : [
            {
              "threadId" : "' . $MessageGroupID . '",
              "readEventDetail" : {
                "eventIndex" : "' . $LastMessageID . '"
              }
            }
          ]
        }';
        
        // Send Request
        Utilities::SendRequest(MESSAGE_USERS_URL . 'me/threads/@threads/read', $headers, false, null, "PUT", $message);
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
    public function Message($PSN, $MessageText = "", $Attachment = "", $MessageType = 1, $AudioLength = "")
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: multipart/form-data; boundary="gc0p4Jq0M2Yt08jU534c0p"',
        );
        $users_body = array(
            "threadDetail" => array
            (
                "threadMembers" => array()
            )
        );
        
        // Handle List of PSN Names
        if (is_array($PSN))
        {
            foreach ($PSN as $Name)
            {
                $users_body["threadDetail"]["threadMembers"][] = array("onlineId" => $Name);
            }
        }
        else
        {
            $users_body["threadDetail"]["threadMembers"][] = array("onlineId" => $PSN);
        }
        $users_body["threadDetail"]["threadMembers"][] = array("onlineId" => $this->me);
        
        $message = "\n";
        $message .= "--gc0p4Jq0M2Yt08jU534c0p\n";
        $message .= "Content-Type: application/json; charset=utf-8\n";
        $message .= "Content-Disposition: form-data; name=\"threadDetail\"\n";
        $message .= "\n";
        
        $message .= json_encode($users_body) . "\n";
                
        // Message Body
        $message_body = array(
            "messageEventDetail" => array
            (
                "messageDetail" => array(),
                "eventCategoryCode" => $MessageType
            )
        );
        if ($MessageType == 1011)
        {
            $message_body["messageEventDetail"]["messageDetail"]["voiceDetail"]["playbackTime"] = $AudioLength;
        }
        $message_body["messageEventDetail"]["messageDetail"]["body"] = $MessageText;
                
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
            $ContentKey = "voiceData";
            $ContentType = "audio/3gpp";
        }
        else if ($MessageType == 3)
        {
            // Image
            $ContentKey = "imageData";
            $ContentType = "image/jpeg";
        }
                
        // Build Message With or Without Content
        if ($Attachment)
        {
            $message .= "\n";
            $message .= "--gc0p4Jq0M2Yt08jU534c0p\n";
            $message .= "Content-Type: application/json; charset=utf-8\n";
            $message .= "Content-Disposition: form-data; name=\"messageEventDetail\"\n";
            $message .= "\n";
            
            $message .= json_encode($message_body) . "\n";
            
            $message .= "--gc0p4Jq0M2Yt08jU534c0p\n";
            $message .= "Content-Type: $ContentType\n";
            $message .= "Content-Disposition: form-data;name=\"" . $ContentKey . "\"\n";
            $message .= "Content-Transfer-Encoding: binary\n";
            $message .= "Content-Length: $AttachmentLength\n";
            $message .= "\n";
            
            $message .= $AttachmentContent . "\n";
            
            $message .= "--gc0p4Jq0M2Yt08jU534c0p--\n\n";
        }
        else
        {
            $message .= "\n";
            $message .= "--gc0p4Jq0M2Yt08jU534c0p\n";
            $message .= "Content-Type: application/json; charset=utf-8\n";
            $message .= "Content-Disposition: form-data; name=\"messageEventDetail\"\n";
            $message .= "\n";
            
            $message .= json_encode($message_body) . "\n";
            
            $message .= "--gc0p4Jq0M2Yt08jU534c0p--\n\n";
        }
                
        $response = \Utilities::SendRequest(MESSAGE_THREADS_URL, $headers, false, null, "POST", $message);
        
        $data = json_decode($response['body'], false);
        
        return $data;
    }
    
    // Send Message to Existing Group
    public function MessageGroup($GroupID, $MessageText = "", $Attachment = "", $MessageType = 1, $AudioLength = "")
    {
        $headers = array(
            'Authorization: Bearer ' . $this->oauth,
            'Content-Type: multipart/form-data; boundary="gc0p4Jq0M2Yt08jU534c0p"',
        );
                
        // Message Body
        $message_body = array(
            "messageEventDetail" => array
            (
                "messageDetail" => array
                (
                    
                ),
                "eventCategoryCode" => $MessageType
            )
        );
        if ($MessageType == 1011)
        {
            $message_body["messageEventDetail"]["messageDetail"]["voiceDetail"]["playbackTime"] = $AudioLength;
        }
        $message_body["messageEventDetail"]["messageDetail"]["body"] = $MessageText;
                
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
            $ContentKey = "voiceData";
            $ContentType = "audio/3gpp";
        }
        else if ($MessageType == 3)
        {
            // Image
            $ContentKey = "imageData";
            $ContentType = "image/jpeg";
        }
                
        // Build Message With or Without Content
        if ($Attachment)
        {
            $message = "\n";
            $message .= "--gc0p4Jq0M2Yt08jU534c0p\n";
            $message .= "Content-Type: application/json; charset=utf-8\n";
            $message .= "Content-Disposition: form-data; name=\"messageEventDetail\"\n";
            $message .= "\n";
            
            $message .= json_encode($message_body) . "\n";
            
            $message .= "--gc0p4Jq0M2Yt08jU534c0p\n";
            $message .= "Content-Type: $ContentType\n";
            $message .= "Content-Disposition: form-data;name=\"" . $ContentKey . "\"\n";
            $message .= "Content-Transfer-Encoding: binary\n";
            $message .= "Content-Length: $AttachmentLength\n";
            $message .= "\n";
            
            $message .= $AttachmentContent . "\n";
            
            $message .= "--gc0p4Jq0M2Yt08jU534c0p--\n\n";
        }
        else
        {
            $message = "\n";
            $message .= "--gc0p4Jq0M2Yt08jU534c0p\n";
            $message .= "Content-Type: application/json; charset=utf-8\n";
            $message .= "Content-Disposition: form-data; name=\"messageEventDetail\"\n";
            $message .= "\n";
            
            $message .= json_encode($message_body) . "\n";
            
            $message .= "--gc0p4Jq0M2Yt08jU534c0p--\n\n";
        }
        
        $response = \Utilities::SendRequest(MESSAGE_THREADS_URL . '/' . $GroupID . '/messages', $headers, false, null, "POST", $message);
        
        $data = json_decode($response['body'], false);
        
        return $data;
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
}

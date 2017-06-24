<?php
namespace PhpSlackBot;

abstract class Base {
    private $name;
    private $client;
    private $user;
    private $context;
    private $thread;
    private $ts;
    private $id;
    private $mentionOnly = false;
    private $channel;
    abstract protected function configure();
    abstract protected function execute($message, $context);

    public function getName() {
        $this->configure();
        return $this->name;
    }

    public function getClient() {
        return $this->client;
    }

    public function getMentionOnly() {
        return $this->mentionOnly;
    }

    public function setMentionOnly($mentionOnly) {
        $this->mentionOnly = $mentionOnly;
    }

    public function setName($name) {
        $this->name = $name;
    }
    public function setThread($thread) {
        $this->thread = $thread;
    }
    public function setTs($ts) {
        $this->ts = $ts;
    }

    public function setClient($client) {
        $this->client = $client;
    }

    public function setChannel($channel) {
        $this->channel = $channel;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    public function getCurrentUser() {
        return $this->user;
    }
    public function getThread() {
        return $this->thread;
    }
    public function getThreadOrTs() {
	$thread=$this->getThread();
	if(strlen($thread)>0):
	    return $thread;
	else:
	    return $this->getTs();
	endif;
    }
    public function getTs() {
        return $this->ts;
    }
    public function getId() {
        return $this->id;
    }

    public function setContext($context) {
        $this->context = $context;
    }

    public function getCurrentContext() {
        return $this->context;
    }

    public function getCurrentChannel() {
        return $this->channel;
    }

    protected function send($channel, $username, $message,$thread="") {
        $id=time().rand(10,99);
	$this->id=$id;
	if(is_array($username)):
		$userOut=implode('',$username);
	else:
		$userOut=(!is_null($username) ? ($username[0]=="!"?"<$username> ":'<@'.$username.'> ') : '');
	endif;
        $response = array(
                          'id' => $id,
                          'type' => 'message',
                          'channel' => $channel,
                          'text' => $userOut.$message
                          );
	if($thread!=""):
            $response['thread_ts'] = $thread;
	endif;
        $this->client->send(json_encode($response));
    }

    protected function getUserNameFromUserId($userId) {
        $username = 'unknown';
        foreach ($this->context['users'] as $user) {
            if ($user['id'] == $userId) {
                $username = $user['name'];
            }
        }
        return $username;
    }

	protected function getUserIdFromUserName($userName) {
		$userId = '';
		$userName = str_replace('@', '', $userName);
		foreach ($this->context['users'] as $user) {
			if ($user['name'] == $userName) {
				$userId = $user['id'];
			}
		}
		if($userName=="channel"||$username=="everyone"||$username=="here"||$username=="group"):
			$userId="!$userName";
		endif;
		return $userId;
	}
	protected function getUserIdFromUserNameArray($userNames) {
		$userId = '';
		static $Ids;
		foreach ($this->context['users'] as $user) {
			$Ids[$user['name']]=$user['id'];
		}
		foreach($userNames as $userName){
			$userName = str_replace('@', '', $userName);
			if($userName=="channel"||$username=="everyone"||$username=="here"||$username=="group"):
				$userId="<!$userName>";
			elseif(isset($Ids[$userName])):
				$userId='<@'.$Ids[$userName].'> ';
			else:
				$userId='';
			endif;
			$out[]=$userId;
		}
		return $out;
	}

    public function getChannelIdFromChannelName($channelName) {
        $channelName = str_replace('#', '', $channelName);
        foreach ($this->context['channels'] as $channel) {
            if ($channel['name'] == $channelName) {
                return $channel['id'];
            }
        }
        foreach ($this->context['groups'] as $group) {
            if ($group['name'] == $channelName) {
                return $group['id'];
            }
        }
        return false;
    }

    protected function getChannelNameFromChannelId($channelId) {
        foreach ($this->context['channels'] as $channel) {
            if ($channel['id'] == $channelId) {
                return $channel['name'];
            }
        }
        foreach ($this->context['groups'] as $group) {
            if ($group['id'] == $channelId) {
                return $group['name'];
            }
        }
        return false;
    }

    protected function getImIdFromUserId($userId) {
        foreach ($this->context['ims'] as $im) {
            if ($im['user'] == $userId) {
                return $im['id'];
            }
        }
        return false;
    }

}

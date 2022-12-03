<?php

namespace Tustin\PlayStation\Enum;

enum MessageType: int
{
    case Text = 1;
    case Image = 3;
    case Video = 210;
    case Audio = 1011;
    case Sticker = 1013;
    case LeftGroup = 2002;
    case ChangedGroupImage = 2004;
    case StartedVoiceChat = 2020;

    // @TODO: Need to map out all of these events.
    case Unknown = -1;
}

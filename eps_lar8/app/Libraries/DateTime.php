<?php

namespace App\Libraries;

use DateTime as BaseDateTime;
use DateTimeInterface as BaseDateTimeInterface;
use DateTimeZone;
use DateInterval;

class DateTime extends BaseDateTime implements BaseDateTimeInterface
{
    // Konstanta yang diwariskan dari DateTimeInterface
    const ATOM = "Y-m-d\\TH:i:sP";
    const COOKIE = "l, d-M-Y H:i:s T";
    const ISO8601 = "Y-m-d\\TH:i:sO";
    const ISO8601_EXPANDED = "X-m-d\\TH:i:sP";
    const RFC822 = "D, d M y H:i:s O";
    const RFC850 = "l, d-M-y H:i:s T";
    const RFC1036 = "D, d M y H:i:s O";
    const RFC1123 = "D, d M Y H:i:s O";
    const RFC7231 = "D, d M Y H:i:s \\G\\M\\T";
    const RFC2822 = "D, d M Y H:i:s O";
    const RFC3339 = "Y-m-d\\TH:i:sP";
    const RFC3339_EXTENDED = "Y-m-d\\TH:i:s.vP";
    const RSS = "D, d M Y H:i:s O";
    const W3C = "Y-m-d\\TH:i:sP";

    // Metode dari kelas DateTime diwariskan, tidak perlu didefinisikan ulang kecuali Anda butuh perilaku khusus
}

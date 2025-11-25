<?php
use Illuminate\Support\Facades\Process;

$u = 'test';              // имя нового пользователя
$k = 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABgQDypRXSot75S98RlrhiOWSjsiEGcaWch8HFnbT/1KuE7JQ8X3hngaCTgP/5OwRzx30cTSPc0mNVUAh+pfbfgR2Z4yPI/ZtS75OxewYrckgBQOSUdNuVHcx2fPClfHtfEKm4EMf39BqhmZVCrBlGPD2pN9amnjUbvYYS8Dk64HOJs78BqLiG8oGNLxG0W3cc+4Z/ShhdA3awHxbhgYjkUARdmgRHONVLCP67ftP+EBFFzJm/92fI+L2Ye5zqz6q+4+7IM5CtB5tXNuBYOctg2VCm8een5owz/8cFMxR0/36otrB+IMm18SQ7oUzc/ho6yxHyKSpn0G8OpkR9HBaF8y9x2LQcWsSg6+eua9VPBGSDdB72fOLsA1Y5n8ZuiyjrK/wYwkmMCejw6d7Jh8EbXMDQ4Q8Ds7GJ4am250eqWCv1a3KTG2lmN2q6HtDMVurVZoRuejmOzcZbavbbrdjz2BRZ50i+Lh5ltEH73+zF26YhBsiqIFFFqkWllCFY4UQnNAM= skyeng@MacBook-Pro-FVFG42M7Q05N.local';

$script = 'curl -fsSL https://tailscale.com/install.sh | sh && sudo tailscale up --auth-key=tskey-auth-kVF3Nfatrz11CNTRL-rUw2BQfGWBiNSMENXYtJBimWKYQKSuSYh';

$result = Process::run(['bash', '-lc', $script]);

$result->successful() ? 'OK' : $result->errorOutput();

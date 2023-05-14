<?php

namespace App\Http\Controllers\Client\Protocols;

use App\Utils\Dict;
use App\Utils\Helper;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Component\Yaml\Yaml;

class Free
{
    public $flag = 'free';
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        $user = $this->user;
        $free = json_decode(file_get_contents("compress.zlib://".base_path().'/resources/free.json'), true);
        $GET = @$_GET['client'];
        if ($GET == null){
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Clash') !== false) {
                header("Location:".Helper::getSubscribeUrl("/api/v1/client/subscribe?token={$user['token']}&flag=free&client=clash"));
            } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Shadowrocket') !== false) {
                header("Location:".Helper::getSubscribeUrl("/api/v1/client/subscribe?token={$user['token']}&flag=free&client=shadowrocket"));
            } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Surge') !== false) {
                header("Location:".Helper::getSubscribeUrl("/api/v1/client/subscribe?token={$user['token']}&flag=free&client=surge"));
            } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Quantumult%20X') !== false) {
                header("Location:".Helper::getSubscribeUrl("/api/v1/client/subscribe?token={$user['token']}&flag=free&client=quantumult%20x"));
            } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Loon') !== false) {
                header("Location:".Helper::getSubscribeUrl("/api/v1/client/subscribe?token={$user['token']}&flag=free&client=loon"));
            } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Surfboard') !== false) {
                header("Location:".Helper::getSubscribeUrl("/api/v1/client/subscribe?token={$user['token']}&flag=free&client=surfboard"));
            } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Shadowsocks') !== false) {
                header("Location:".Helper::getSubscribeUrl("/api/v1/client/subscribe?token={$user['token']}&flag=free&client=shadowsocks"));
            } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Stash') !== false) {
                header("Location:".Helper::getSubscribeUrl("/api/v1/client/subscribe?token={$user['token']}&flag=free&client=stash"));
            }
        }
        $GET = strtolower($GET);
        switch ($GET){
            case 'info':
                echo '<meta http-equiv="Content-Type" content="text/html;charset=utf-8"><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><meta content="always" name="referrer"><title>软体兼容性|' . config('v2board.app_name', 'V2Board') . ' Free</title>请在网址后加入 &client=识别码 后导入到客户端<br><table border="1x"><tr><td>客户端</td><td>识别码</td></tr><tr><td>Clash</td><td>clash</td></tr><tr><td>Shadowrocket</td><td>shadowrocket</td></tr><tr><td>Surge</td><td>surge</td></tr><tr><td>Quantumult</td><td>Vmess: quantumult-vmess<br>SSR: quantumult-ssr<br>SS: quantumult-ss</td></tr><tr><td>QuantumultX</td><td>quantumult%20x</td></tr><tr><td>Loon</td><td>loon</td></tr><tr><td>Loon Lite</td><td>loon</td></tr><tr><td>Surfboard</td><td>surfboard</td></tr><tr><td>V2rayNG</td><td>v2rayng</td></tr><tr><td>V2rayN</td><td>v2rayn</td></tr><tr><td>Passwall</td><td>passwall</td></tr><tr><td>SSRPlus</td><td>ssrplus</td></tr><tr><td>Shadowsocks</td><td>shadowsocks</td></tr><tr><td>AnXray</td><td>aaxray</td></tr><tr><td>Stash</td><td>stash</td></tr></table>对应客户端和识别码请见上表<br><br>节点信息：<br>';
                foreach ($free as $item) {
                    echo $item['type'].' - '.$item['name'].'<br>';
                }
            break;
            default:
                $uri = '';
                foreach ($free as $item) {
                    if ($item['type'] === 'shadowsocks') {
                        $name = rawurlencode($item['name']);
                        $str = str_replace(
                            ['+', '/', '='],
                            ['-', '_', ''],
                            base64_encode("{$item['cipher']}:{$item['password']}")
                        );
                        $uri .= "ss://{$str}@{$item['host']}:{$item['port']}#{$name}\r\n";
                    }
                    if ($item['type'] === 'shadowsocksR') {
                        $protocol_type = "";
                        $protocol_param = "";
                        $obfs_type = "";
                        $obfs_param = "";
                        if ($item['protocol']['type']!= null) {
                            $protocol_type .= "{$item['protocol']['type']}:";
                            if ($item['protocol']['param']!= null) {
                                $protocol_param .= "protoparam=".base64_encode("{$item['protocol']['param']}")."&";
                            }
                        }
                        if ($item['obfs']['type']!= null) {
                            $obfs_type .= "{$item['obfs']['type']}:";
                            if ($item['obfs']['param']!= null) {
                                $obfs_param .= "obfsparam=".base64_encode("{$item['obfs']['param']}")."&";
                            }
                        }
                        $uri .= "ssr://".base64_encode("{$item['host']}:{$item['port']}:".$protocol_type."{$item['cipher']}:".$obfs_type.base64_encode("{$item['password']}")."/?".$obfs_param.$protocol_param."remarks=".base64_encode("{$item['name']}")."\r\n");
                    }
                    if ($item['type'] === 'vmess') {
                        $userinfo = base64_encode($item['cipher'] . ':' . $item['password'] . '@' . $item['host'] . ':' . $item['port']);
                        $config = [
                            'tfo' => 1,
                            'remark' => $item['name'],
                            'alterId' => $item['alterId']
                        ];
                        if ($item['tls']) {
                            $config['tls'] = 1;
                            if ($item['tlsSettings']) {
                                $tlsSettings = $item['tlsSettings'];
                                if (isset($tlsSettings['allowInsecure']) && !empty($tlsSettings['allowInsecure']))
                                    $config['allowInsecure'] = (int)$tlsSettings['allowInsecure'];
                                if (isset($tlsSettings['serverName']) && !empty($tlsSettings['serverName']))
                                    $config['peer'] = $tlsSettings['serverName'];
                            }
                        }
                        if ($item['network'] === 'ws') {
                            $config['obfs'] = "websocket";
                            if ($item['networkSettings']) {
                                $wsSettings = $item['networkSettings'];
                                if (isset($wsSettings['path']) && !empty($wsSettings['path']))
                                    $config['path'] = $wsSettings['path'];
                                if (isset($wsSettings['headers']['Host']) && !empty($wsSettings['headers']['Host']))
                                    $config['obfsParam'] = $wsSettings['headers']['Host'];
                            }
                        }
                        if ($item['network'] === 'grpc') {
                            $config['obfs'] = "grpc";
                            if ($item['networkSettings']) {
                                $grpcSettings = $item['networkSettings'];
                                if (isset($grpcSettings['serviceName']) && !empty($grpcSettings['serviceName']))
                                    $config['path'] = $grpcSettings['serviceName'];
                            }
                            if (isset($tlsSettings)) {
                                $config['host'] = $tlsSettings['serverName'];
                            } else {
                                $config['host'] = $item['host'];
                            }
                        }
                        $query = http_build_query($config, '', '&', PHP_QUERY_RFC3986);
                        $uri .= "vmess://{$userinfo}?{$query}\r\n";
                    }
                    if ($item['type'] === 'trojan') {
                        $name = rawurlencode($item['name']);
                        $query = http_build_query([
                            'allowInsecure' => $item['allow_insecure'],
                            'peer' => $item['server_name']
                        ]);
                        $uri .= "trojan://{$item['password']}@{$item['host']}:{$item['port']}?{$query}&tfo=1#{$name}\r\n";
                    }
                }
                return base64_encode($uri);
            break;
            case 'sagernet':
                $uri = '';

                foreach ($free as $item) {
                    if ($item['type'] === 'shadowsocks') {
                        $name = rawurlencode($item['name']);
                        $str = str_replace(
                            ['+', '/', '='],
                            ['-', '_', ''],
                            base64_encode("{$item['cipher']}:{$item['password']}")
                        );
                        $uri .= "ss://{$str}@{$item['host']}:{$item['port']}#{$name}\r\n";
                    }
                    if ($item['type'] === 'shadowsocksR') {
                        $protocol_type = "";
                        $protocol_param = "";
                        $obfs_type = "";
                        $obfs_param = "";
                        if ($item['protocol']['type']!= null) {
                            $protocol_type .= "{$item['protocol']['type']}:";
                            if ($item['protocol']['param']!= null) {
                                $protocol_param .= "protoparam=".base64_encode("{$item['protocol']['param']}")."&";
                            }
                        }
                        if ($item['obfs']['type']!= null) {
                            $obfs_type .= "{$item['obfs']['type']}:";
                            if ($item['obfs']['param']!= null) {
                                $obfs_param .= "obfsparam=".base64_encode("{$item['obfs']['param']}")."&";
                            }
                        }
                        $uri .= "ssr://".base64_encode("{$item['host']}:{$item['port']}:".$protocol_type."{$item['cipher']}:".$obfs_type.base64_encode("{$item['password']}")."/?".$obfs_param.$protocol_param."remarks=".base64_encode("{$item['name']}")."\r\n");
                    }
                    if ($item['type'] === 'vmess') {
                        $config = [
                            "encryption" => $item['cipher'],
                            "type" => urlencode($item['network']),
                            "security" => $item['tls'] ? "tls" : "",
                        ];
                        if ($item['tls']) {
                            if ($item['tlsSettings']) {
                                $tlsSettings = $item['tlsSettings'];
                                if (isset($tlsSettings['serverName']) && !empty($tlsSettings['serverName']))
                                $config['sni'] = urlencode($tlsSettings['serverName']);
                            }
                        }
                        if ((string)$item['network'] === 'ws') {
                            $wsSettings = $item['networkSettings'];
                            if (isset($wsSettings['path'])) $config['path'] = $wsSettings['path'];
                            if (isset($wsSettings['headers']['Host'])) $config['host'] = urlencode($wsSettings['headers']['Host']);
                        }
                        if ((string)$item['network'] === 'grpc') {
                            $grpcSettings = $item['networkSettings'];
                            if (isset($grpcSettings['serviceName'])) $config['serviceName'] = urlencode($grpcSettings['serviceName']);
                        }
                        $uri .= "vmess://" . $item['password'] . "@" . $item['host'] . ":" . $item['port'] . "?" . http_build_query($config) . "#" . urlencode($item['name']) . "\r\n";
                    }
                    if ($item['type'] === 'trojan') {
                        $name = rawurlencode($item['name']);
                        $query = http_build_query([
                            'allowInsecure' => $item['allow_insecure'],
                            'peer' => $item['server_name'],
                            'sni' => $item['server_name']
                        ]);
                        $uri .= "trojan://{$item['password']}@{$item['host']}:{$item['port']}?{$query}#{$name}" . "\r\n";
                    }
                }
                return base64_encode($uri);
            break;
            case 'clash':
                $appName = config('v2board.app_name', 'V2Board')." Free";
                header("subscription-userinfo: expire={$user['expired_at']}");
                header('profile-update-interval: 24');
                header("content-disposition:attachment;filename*=UTF-8''".rawurlencode($appName));
                header("profile-web-page-url:" . config('v2board.app_url'));
                $defaultConfig = base_path() . '/resources/rules/default.clash.yaml';
                $customConfig = base_path() . '/resources/rules/custom.clash.yaml';
                if (\File::exists($customConfig)) {
                    $config = Yaml::parseFile($customConfig);
                } else {
                    $config = Yaml::parseFile($defaultConfig);
                }
                preg_match('#(ClashX)[/ ]([0-9.]*)#', $_SERVER['HTTP_USER_AGENT'], $matches);
                if (isset($matches[2]) && $matches[2] < '1.96.2') $config['dns']['enhanced-mode'] = 'redir-host';
                $proxy = [];
                $proxies = [];
                foreach ($free as $item) {
                    if ($item['type'] === 'shadowsocks'
                        && in_array($item['cipher'], [
                            'aes-128-gcm',
                            'aes-192-gcm',
                            'aes-256-gcm',
                            'chacha20-ietf-poly1305'
                        ])
                    ) {
                        $array = [];
                        $array['name'] = $item['name'];
                        $array['type'] = 'ss';
                        $array['server'] = $item['host'];
                        $array['port'] = $item['port'];
                        $array['cipher'] = $item['cipher'];
                        $array['password'] = $item['password'];
                        $array['udp'] = true;
                        array_push($proxy, $array);
                        array_push($proxies, $item['name']);
                    }
                    if ($item['type'] === 'shadowsocksR'
                        && in_array($item['cipher'], [
                            'aes-128-gcm',
                            'aes-192-gcm',
                            'aes-256-gcm',
                            'aes-128-cfb',
                            'aes-192-cfb',
                            'aes-256-cfb',
                            'aes-128-ctr',
                            'aes-192-ctr',
                            'rc4-md5',
                            'chacha20',
                            'xchacha20',
                            'chacha20-ietf-poly1305',
                            'xchacha20-ietf-poly1305'
                        ])
                    ) {
                        $array = [];
                        $array['name'] = $item['name'];
                        $array['type'] = 'ssr';
                        $array['server'] = $item['host'];
                        $array['port'] = $item['port'];
                        $array['cipher'] = $item['cipher'];
                        $array['password'] = $item['password'];
                        if ($item['protocol']['type']!= null) {
                            $array['protocol'] = $item['protocol']['type'];
                            if ($item['protocol']['param']!= null) {
                                $array['protocol-param'] = $item['protocol']['param'];
                            }
                        }
                        if ($item['obfs']['type']!= null) {
                            $array['obfs'] = $item['obfs']['type'];
                            if ($item['obfs']['param']!= null) {
                                $array['obfs-param'] = $item['obfs']['param'];
                            }
                        }
                        $array['udp'] = true;
                        array_push($proxy, $array);
                        array_push($proxies, $item['name']);
                    }
                    if ($item['type'] === 'vmess') {
                        $array = [];
                        $array['name'] = $item['name'];
                        $array['type'] = 'vmess';
                        $array['server'] = $item['host'];
                        $array['port'] = $item['port'];
                        $array['uuid'] = $item['password'];
                        $array['alterId'] = $item['alterId'];
                        $array['cipher'] = $item['cipher'];
                        $array['udp'] = true;
                        if ($item['tls']) {
                            $array['tls'] = true;
                            if ($item['tlsSettings']) {
                                $tlsSettings = $item['tlsSettings'];
                                if (isset($tlsSettings['allowInsecure']) && !empty($tlsSettings['allowInsecure']))
                                    $array['skip-cert-verify'] = ($tlsSettings['allowInsecure'] ? true : false);
                                if (isset($tlsSettings['serverName']) && !empty($tlsSettings['serverName']))
                                    $array['servername'] = $tlsSettings['serverName'];
                            }
                        }
                        if ($item['network'] === 'ws') {
                            $array['network'] = 'ws';
                            if ($item['networkSettings']) {
                                $wsSettings = $item['networkSettings'];
                                $array['ws-opts'] = [];
                                if (isset($wsSettings['path']) && !empty($wsSettings['path']))
                                    $array['ws-opts']['path'] = $wsSettings['path'];
                                if (isset($wsSettings['headers']['Host']) && !empty($wsSettings['headers']['Host']))
                                    $array['ws-opts']['headers'] = ['Host' => $wsSettings['headers']['Host']];
                                if (isset($wsSettings['path']) && !empty($wsSettings['path']))
                                    $array['ws-path'] = $wsSettings['path'];
                                if (isset($wsSettings['headers']['Host']) && !empty($wsSettings['headers']['Host']))
                                    $array['ws-headers'] = ['Host' => $wsSettings['headers']['Host']];
                            }
                        }
                        if ($item['network'] === 'grpc') {
                            $array['network'] = 'grpc';
                            if ($item['networkSettings']) {
                                $grpcSettings = $item['networkSettings'];
                                $array['grpc-opts'] = [];
                                if (isset($grpcSettings['serviceName'])) $array['grpc-opts']['grpc-service-name'] = $grpcSettings['serviceName'];
                            }
                        }
                        array_push($proxy, $array);
                        array_push($proxies, $item['name']);
                    }
                    if ($item['type'] === 'trojan') {
                        $array = [];
                        $array['name'] = $item['name'];
                        $array['type'] = 'trojan';
                        $array['server'] = $item['host'];
                        $array['port'] = $item['port'];
                        $array['password'] = $item['password'];
                        $array['udp'] = true;
                        if (!empty($item['server_name'])) $array['sni'] = $item['server_name'];
                        if (!empty($item['allow_insecure'])) $array['skip-cert-verify'] = ($item['allow_insecure'] ? true : false);
                        array_push($proxy, $array);
                        array_push($proxies, $item['name']);
                    }
                }
                $config['proxies'] = array_merge($config['proxies'] ? $config['proxies'] : [], $proxy);
                foreach ($config['proxy-groups'] as $k => $v) {
                    if (!is_array($config['proxy-groups'][$k]['proxies'])) $config['proxy-groups'][$k]['proxies'] = [];
                    $isFilter = false;
                    foreach ($config['proxy-groups'][$k]['proxies'] as $src) {
                        foreach ($proxies as $dst) {
                            if (!@preg_match($src, null) !== false) continue;
                            $isFilter = true;
                            $config['proxy-groups'][$k]['proxies'] = array_values(array_diff($config['proxy-groups'][$k]['proxies'], [$src]));
                            if (@preg_match($src, $dst)) {
                                array_push($config['proxy-groups'][$k]['proxies'], $dst);
                            }
                        }
                        if ($isFilter) continue;
                    }
                    if ($isFilter) continue;
                    $config['proxy-groups'][$k]['proxies'] = array_merge($config['proxy-groups'][$k]['proxies'], $proxies);
                }
                // Force the current subscription domain to be a direct rule
                $subsDomain = $_SERVER['HTTP_HOST'];
                if ($subsDomain) {
                    array_unshift($config['rules'], "DOMAIN,{$subsDomain},DIRECT");
                }
                $yaml = Yaml::dump($config, 2, 4, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE);
                $yaml = str_replace('$app_name', config('v2board.app_name', 'V2Board')." Free", $yaml);
                return $yaml;
            break;
            case 'stash':
                $appName = config('v2board.app_name', 'V2Board')." Free";
                header("subscription-userinfo: expire={$user['expired_at']}");
                header('profile-update-interval: 24');
                header("content-disposition:attachment;filename*=UTF-8''".rawurlencode($appName));
                // 暂时使用clash配置文件，后续根据Stash更新情况更新
                $defaultConfig = base_path() . '/resources/rules/default.clash.yaml';
                $customConfig = base_path() . '/resources/rules/custom.clash.yaml';
                if (\File::exists($customConfig)) {
                    $config = Yaml::parseFile($customConfig);
                } else {
                    $config = Yaml::parseFile($defaultConfig);
                }
                $proxy = [];
                $proxies = [];

                foreach ($free as $item) {
                    if ($item['type'] === 'shadowsocks'
                        && in_array($item['cipher'], [
                            'aes-128-gcm',
                            'aes-192-gcm',
                            'aes-256-gcm',
                            'chacha20-ietf-poly1305'
                        ])
                    ) {
                        $array = [];
                        $array['name'] = $item['name'];
                        $array['type'] = 'ss';
                        $array['server'] = $item['host'];
                        $array['port'] = $item['port'];
                        $array['cipher'] = $item['cipher'];
                        $array['password'] = $item['password'];
                        $array['udp'] = true;
                        array_push($proxy, $array);
                        array_push($proxies, $item['name']);
                    }
                    if ($item['type'] === 'shadowsocksR'
                        && in_array($item['cipher'], [
                            'aes-128-gcm',
                            'aes-192-gcm',
                            'aes-256-gcm',
                            'aes-128-cfb',
                            'aes-192-cfb',
                            'aes-256-cfb',
                            'aes-128-ctr',
                            'aes-192-ctr',
                            'rc4-md5',
                            'chacha20',
                            'xchacha20',
                            'chacha20-ietf-poly1305',
                            'xchacha20-ietf-poly1305'
                        ])
                    ) {
                        $array = [];
                        $array['name'] = $item['name'];
                        $array['type'] = 'ssr';
                        $array['server'] = $item['host'];
                        $array['port'] = $item['port'];
                        $array['cipher'] = $item['cipher'];
                        $array['password'] = $item['password'];
                        if ($item['protocol']['type']!= null) {
                            $array['protocol'] = $item['protocol']['type'];
                            if ($item['protocol']['param']!= null) {
                                $array['protocol-param'] = $item['protocol']['param'];
                            }
                        }
                        if ($item['obfs']['type']!= null) {
                            $array['obfs'] = $item['obfs']['type'];
                            if ($item['obfs']['param']!= null) {
                                $array['obfs-param'] = $item['obfs']['param'];
                            }
                        }
                        $array['udp'] = true;
                        array_push($proxy, $array);
                        array_push($proxies, $item['name']);
                    }
                    if ($item['type'] === 'vmess') {
                        $array = [];
                        $array['name'] = $item['name'];
                        $array['type'] = 'vmess';
                        $array['server'] = $item['host'];
                        $array['port'] = $item['port'];
                        $array['uuid'] = $item['password'];
                        $array['alterId'] = $item['alterId'];
                        $array['cipher'] = $item['cipher'];
                        $array['udp'] = true;

                        if ($item['tls']) {
                            $array['tls'] = true;
                            if ($item['tlsSettings']) {
                                $tlsSettings = $item['tlsSettings'];
                                if (isset($tlsSettings['allowInsecure']) && !empty($tlsSettings['allowInsecure']))
                                    $array['skip-cert-verify'] = ($tlsSettings['allowInsecure'] ? true : false);
                                if (isset($tlsSettings['serverName']) && !empty($tlsSettings['serverName']))
                                    $array['servername'] = $tlsSettings['serverName'];
                            }
                        }
                        if ($item['network'] === 'ws') {
                            $array['network'] = 'ws';
                            if ($item['networkSettings']) {
                                $wsSettings = $item['networkSettings'];
                                $array['ws-opts'] = [];
                                if (isset($wsSettings['path']) && !empty($wsSettings['path']))
                                    $array['ws-opts']['path'] = $wsSettings['path'];
                                if (isset($wsSettings['headers']['Host']) && !empty($wsSettings['headers']['Host']))
                                    $array['ws-opts']['headers'] = ['Host' => $wsSettings['headers']['Host']];
                                if (isset($wsSettings['path']) && !empty($wsSettings['path']))
                                    $array['ws-path'] = $wsSettings['path'];
                                if (isset($wsSettings['headers']['Host']) && !empty($wsSettings['headers']['Host']))
                                    $array['ws-headers'] = ['Host' => $wsSettings['headers']['Host']];
                            }
                        }
                        if ($item['network'] === 'grpc') {
                            $array['network'] = 'grpc';
                            if ($item['networkSettings']) {
                                $grpcSettings = $item['networkSettings'];
                                $array['grpc-opts'] = [];
                                if (isset($grpcSettings['serviceName']))  $array['grpc-opts']['grpc-service-name'] = $grpcSettings['serviceName'];
                            }
                        }
                        array_push($proxy, $array);
                        array_push($proxies, $item['name']);
                    }
                    if ($item['type'] === 'trojan') {
                        $array = [];
                        $array['name'] = $item['name'];
                        $array['type'] = 'trojan';
                        $array['server'] = $item['host'];
                        $array['port'] = $item['port'];
                        $array['password'] = $item['password'];
                        $array['udp'] = true;
                        if (!empty($item['server_name'])) $array['sni'] = $item['server_name'];
                        if (!empty($item['allow_insecure'])) $array['skip-cert-verify'] = ($item['allow_insecure'] ? true : false);
                        array_push($proxy, $array);
                        array_push($proxies, $item['name']);
                    }
                }

                $config['proxies'] = array_merge($config['proxies'] ? $config['proxies'] : [], $proxy);
                foreach ($config['proxy-groups'] as $k => $v) {
                    if (!is_array($config['proxy-groups'][$k]['proxies'])) continue;
                    $isFilter = false;
                    foreach ($config['proxy-groups'][$k]['proxies'] as $src) {
                        foreach ($proxies as $dst) {
                            if (!@preg_match($src, null) !== false) continue;
                            $isFilter = true;
                            $config['proxy-groups'][$k]['proxies'] = array_values(array_diff($config['proxy-groups'][$k]['proxies'], [$src]));
                            try {
                                if (preg_match($src, $dst)) {
                                    array_push($config['proxy-groups'][$k]['proxies'], $dst);
                                }
                            } catch (\Exception $e) {
                                if (false) {
                                    array_push($config['proxy-groups'][$k]['proxies'], $dst);
                                }
                            }
                        }
                        if ($isFilter) continue;
                    }
                    if ($isFilter) continue;
                    $config['proxy-groups'][$k]['proxies'] = array_merge($config['proxy-groups'][$k]['proxies'], $proxies);
                }
                // Force the current subscription domain to be a direct rule
                $subsDomain = $_SERVER['HTTP_HOST'];
                if ($subsDomain) {
                    array_unshift($config['rules'], "DOMAIN,{$subsDomain},DIRECT");
                }

                $yaml = Yaml::dump($config, 2, 4, Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE);
                $yaml = str_replace('$app_name', config('v2board.app_name', 'V2Board')." Free", $yaml);
                return $yaml;
            break;
            case 'shadowrocket':
                $uri = '';
                $expiredDate = date('Y-m-d', $user['expired_at']);
                $uri .= "STATUS=💡Expires:{$expiredDate}\r\n";
                foreach ($free as $item) {
                    if ($item['type'] === 'shadowsocks') {
                        if ($item['cipher'] === '2022-blake3-aes-128-gcm') {
                            $serverKey = Helper::getShadowsocksServerKey($item['created_at'], 16);
                            $userKey = Helper::uuidToBase64($item['password'], 16);
                            $password = "{$serverKey}:{$userKey}";
                        } elseif ($item['cipher'] === '2022-blake3-aes-256-gcm') {
                            $serverKey = Helper::getShadowsocksServerKey($item['created_at'], 32);
                            $userKey = Helper::uuidToBase64($item['password'], 32);
                            $password = "{$serverKey}:{$userKey}";
                        } else {
                            $password = $item['password'];
                        }
                        $name = rawurlencode($item['name']);
                        $str = str_replace(
                            ['+', '/', '='],
                            ['-', '_', ''],
                            base64_encode("{$item['cipher']}:{$password}")
                        );
                        $uri .= "ss://{$str}@{$item['host']}:{$item['port']}#{$name}\r\n";
                    }
                    if ($item['type'] === 'shadowsocksR') {
                        $protocol_type = "";
                        $protocol_param = "";
                        $obfs_type = "";
                        $obfs_param = "";
                        if ($item['protocol']['type']!= null) {
                            $protocol_type .= "{$item['protocol']['type']}:";
                            if ($item['protocol']['param']!= null) {
                                $protocol_param .= "protoparam=".base64_encode("{$item['protocol']['param']}")."&";
                            }
                        }
                        if ($item['obfs']['type']!= null) {
                            $obfs_type .= "{$item['obfs']['type']}:";
                            if ($item['obfs']['param']!= null) {
                                $obfs_param .= "obfsparam=".base64_encode("{$item['obfs']['param']}")."&";
                            }
                        }
                        $uri .= "ssr://".base64_encode("{$item['host']}:{$item['port']}:".$protocol_type."{$item['cipher']}:".$obfs_type.base64_encode("{$item['password']}")."/?".$obfs_param.$protocol_param."remarks=".base64_encode("{$item['name']}")."\r\n");
                    }
                    if ($item['type'] === 'vmess') {
                        $userinfo = base64_encode($item['cipher'] . ':' . $item['password'] . '@' . $item['host'] . ':' . $item['port']);
                        $config = [
                            'tfo' => 1,
                            'remark' => $item['name'],
                            'alterId' => $item['alterId']
                        ];
                        if ($item['tls']) {
                            $config['tls'] = 1;
                            if ($item['tlsSettings']) {
                                $tlsSettings = $item['tlsSettings'];
                                if (isset($tlsSettings['allowInsecure']) && !empty($tlsSettings['allowInsecure']))
                                    $config['allowInsecure'] = (int)$tlsSettings['allowInsecure'];
                                if (isset($tlsSettings['serverName']) && !empty($tlsSettings['serverName']))
                                    $config['peer'] = $tlsSettings['serverName'];
                            }
                        }
                        if ($item['network'] === 'ws') {
                            $config['obfs'] = "websocket";
                            if ($item['networkSettings']) {
                                $wsSettings = $item['networkSettings'];
                                if (isset($wsSettings['path']) && !empty($wsSettings['path']))
                                    $config['path'] = $wsSettings['path'];
                                if (isset($wsSettings['headers']['Host']) && !empty($wsSettings['headers']['Host']))
                                    $config['obfsParam'] = $wsSettings['headers']['Host'];
                            }
                        }
                        if ($item['network'] === 'grpc') {
                            $config['obfs'] = "grpc";
                            if ($item['networkSettings']) {
                                $grpcSettings = $item['networkSettings'];
                                if (isset($grpcSettings['serviceName']) && !empty($grpcSettings['serviceName']))
                                    $config['path'] = $grpcSettings['serviceName'];
                            }
                            if (isset($tlsSettings)) {
                                $config['host'] = $tlsSettings['serverName'];
                            } else {
                                $config['host'] = $item['host'];
                            }
                        }
                        $query = http_build_query($config, '', '&', PHP_QUERY_RFC3986);
                        $uri .= "vmess://{$userinfo}?{$query}\r\n";
                    }
                    if ($item['type'] === 'trojan') {
                        $name = rawurlencode($item['name']);
                        $query = http_build_query([
                            'allowInsecure' => $item['allow_insecure'],
                            'peer' => $item['server_name']
                        ]);
                        $uri .= "trojan://{$item['password']}@{$item['host']}:{$item['port']}?{$query}&tfo=1#{$name}\r\n";
                    }
                }
                return base64_encode($uri);
            break;
            case 'surge':
                $appName = config('v2board.app_name', 'V2Board')." Free";
                header("content-disposition:attachment;filename*=UTF-8''".rawurlencode($appName).".conf");

                $proxies = '';
                $proxyGroup = '';

                foreach ($free as $item) {
                    if ($item['type'] === 'shadowsocks'
                    && in_array($item['cipher'], [
                        'aes-128-gcm',
                        'aes-192-gcm',
                        'aes-256-gcm',
                        'chacha20-ietf-poly1305'
                        ])
                    ) {
                    // [Proxy]
                    $config = [
                        "{$item['name']}=ss",
                        "{$item['host']}",
                        "{$item['port']}",
                        "encrypt-method={$item['cipher']}",
                        "password={$item['password']}",
                        'tfo=true',
                        'udp-relay=true'
                    ];
                    $config = array_filter($config);
                    $proxies .= implode(',', $config) . "\r\n";
                    // [Proxy Group]
                    $proxyGroup .= $item['name'] . ', ';
                    }
                    if ($item['type'] === 'vmess') {
                        if ($item['alterId'] == 0){
                            $aead = "vmess-aead=true";
                        } else {
                            $aead = "vmess-aead=false";
                        }
                        // [Proxy]
                        $config = [
                            "{$item['name']}=vmess",
                            "{$item['host']}",
                            "{$item['port']}",
                            "username={$item['password']}",
                            "{$aead}",
                            'tfo=true',
                            'udp-relay=true'
                        ];
                        
                        if ($item['tls']) {
                            array_push($config, 'tls=true');
                            if ($item['tlsSettings']) {
                                $tlsSettings = $item['tlsSettings'];
                                if (isset($tlsSettings['allowInsecure']) && !empty($tlsSettings['allowInsecure']))
                                    array_push($config, 'skip-cert-verify=' . ($tlsSettings['allowInsecure'] ? 'true' : 'false'));
                                if (isset($tlsSettings['serverName']) && !empty($tlsSettings['serverName']))
                                    array_push($config, "sni={$tlsSettings['serverName']}");
                            }
                        }
                        if ($item['network'] === 'ws') {
                            array_push($config, 'ws=true');
                            if ($item['networkSettings']) {
                                $wsSettings = $item['networkSettings'];
                                if (isset($wsSettings['path']) && !empty($wsSettings['path']))
                                    array_push($config, "ws-path={$wsSettings['path']}");
                                    if (isset($wsSettings['headers']['Host']) && !empty($wsSettings['headers']['Host']))
                                        array_push($config, "ws-headers=Host:{$wsSettings['headers']['Host']}");
                            }
                        }
                        $proxies .= implode(',', $config) . "\r\n";
                        // [Proxy Group]
                        $proxyGroup .= $item['name'] . ', ';
                    }
                    if ($item['type'] === 'trojan') {
                        // [Proxy]
                        $config = [
                            "{$item['name']}=trojan",
                            "{$item['host']}",
                            "{$item['port']}",
                            "password={$item['password']}",
                            $item['server_name'] ? "sni={$item['server_name']}" : "",
                            'tfo=true',
                            'udp-relay=true'
                        ];
                        if (!empty($item['allow_insecure'])) {
                            array_push($config, $item['allow_insecure'] ? 'skip-cert-verify=true' : 'skip-cert-verify=false');
                        }
                        $config = array_filter($config);
                        $proxies .= implode(',', $config) . "\r\n";
                        // [Proxy Group]
                        $proxyGroup .= $item['name'] . ', ';
                    }
                }
                
                $defaultConfig = base_path() . '/resources/rules/default.surge.conf';
                $customConfig = base_path() . '/resources/rules/custom.surge.conf';
                if (\File::exists($customConfig)) {
                    $config = file_get_contents("$customConfig");
                } else {
                    $config = file_get_contents("$defaultConfig");
                }
                
                // Subscription link
                $subsURL = Helper::getSubscribeUrl("/api/v1/client/subscribe?token={$user['token']}&flag=free&client=surge");
                $subsDomain = $_SERVER['HTTP_HOST'];
                $subsURL = 'https://' . $subsDomain . '/api/v1/client/subscribe?token=' . $user['token'] . '&flag=free&client=surge';
                
                $config = str_replace('$subs_link', $subsURL, $config);
                $config = str_replace('$subs_domain', $subsDomain, $config);
                $config = str_replace('$proxies', $proxies, $config);
                $config = str_replace('$proxy_group', rtrim($proxyGroup, ', '), $config);

                $expireDate = $user['expired_at'] === NULL ? '长期有效' : date('Y-m-d H:i:s', $user['expired_at']);
                $subscribeInfo = "title={$appName}订阅信息, content=到期时间：{$expireDate}";
                $config = str_replace('$subscribe_info', $subscribeInfo, $config);
                
                return $config;
            break;
            case 'quantumult-vmess':
                $uri = '';
                header("subscription-userinfo: expire={$user['expired_at']}");
                foreach ($free as $item) {
                    if ($item['type'] === 'vmess') {
                        $str = '';
                        $tlsSettings_host = "";
                        if ($item['tls']) {
                            if ($item['tlsSettings']) {
                                $tlsSettings = $item['tlsSettings'];
                                if (isset($tlsSettings['serverName']) && !empty($tlsSettings['serverName']))
                                    $tlsSettings_host .= ', tls-host=' . $tlsSettings['serverName'];
                            }
                        }
                        $str .= $item['name'] . '= vmess, ' . $item['host'] . ', ' . $item['port'] . ', ' . $item['cipher']. ', "' . $item['password'] . '", over-tls=' . ($item['tls'] ? "true" : "false") . $tlsSettings_host . ', certificate=0, group=' . config('v2board.app_name', 'V2Board')." Free_Vmess";
                        if ($item['network'] === 'ws') {
                            $str .= ', obfs=ws';
                            if ($item['networkSettings']) {
                                $wsSettings = $item['networkSettings'];
                                if (isset($wsSettings['path'])) $str .= ', obfs-path="' . $wsSettings['path'] . '"';
                                if (isset($wsSettings['headers']['Host'])) $str .= ', obfs-header="Host:' . $wsSettings['headers']['Host'] . '"';
                            }
                        }
                        $uri .= "vmess://" . base64_encode($str) . "\r\n";
                    }
                }
                return base64_encode($uri);
            break;
            case 'quantumult-ss':
                $uri = '';
                header("subscription-userinfo: expire={$user['expired_at']}");
                foreach ($free as $item) {
                    if ($item['type'] === 'shadowsocks') {
                        $name = rawurlencode($item['name']);
                        $str = str_replace(
                            ['+', '/', '='],
                            ['-', '_', ''],
                            base64_encode("{$item['cipher']}:{$item['password']}")
                        );
                        $uri .= "ss://{$str}@{$item['host']}:{$item['port']}/?group=" . base64_encode(config('v2board.app_name', 'V2Board')) ." Free_SS" . "#{$name}\r\n";
                    }
                }
                return base64_encode($uri);
            break;
            case 'quantumult-ssr':
                $uri = '';
                header("subscription-userinfo: expire={$user['expired_at']}");
                foreach ($free as $item) {
                    if ($item['type'] === 'shadowsocksR') {
                        $protocol_type = "";
                        $protocol_param = "";
                        $obfs_type = "";
                        $obfs_param = "";
                        if ($item['protocol']['type']!= null) {
                            $protocol_type .= "{$item['protocol']['type']}:";
                            if ($item['protocol']['param']!= null) {
                                $protocol_param .= "protoparam=".base64_encode("{$item['protocol']['param']}")."&";
                            }
                        }
                        if ($item['obfs']['type']!= null) {
                            $obfs_type .= "{$item['obfs']['type']}:";
                            if ($item['obfs']['param']!= null) {
                                $obfs_param .= "obfsparam=".base64_encode("{$item['obfs']['param']}")."&";
                            }
                        }
                        $uri .= "ssr://".base64_encode("{$item['host']}:{$item['port']}:".$protocol_type."{$item['cipher']}:".$obfs_type.base64_encode("{$item['password']}")."/?".$obfs_param.$protocol_param."remarks=".base64_encode("{$item['name']}")."&group=".base64_encode(config('v2board.app_name', 'V2Board')." Free_SSR")."\r\n");
                    }
                }
                return base64_encode($uri);
            break;
            case 'quantumult x':
                $uri = '';
                header("subscription-userinfo: expire={$user['expired_at']}");
                foreach ($free as $item) {
                    if ($item['type'] === 'shadowsocks') {
                        $config = [
                            "shadowsocks={$item['host']}:{$item['port']}",
                            "method={$item['cipher']}",
                            "password={$item['password']}",
                            'fast-open=true',
                            'udp-relay=true',
                            "tag={$item['name']}"
                        ];
                        $config = array_filter($config);
                        $uri .= implode(',', $config). "\r\n";
                    }
                    if ($item['type'] === 'shadowsocksR') {
                        $protocol_type = "";
                        $protocol_param = "";
                        $obfs_type = "";
                        $obfs_param = "";
                        if ($item['protocol']['type']!= null) {
                            $protocol_type .= "ssr-protocol={$item['protocol']['type']}";
                            if ($item['protocol']['param']!= null) {
                                $protocol_param .= "ssr-protocol-param={$item['protocol']['param']}";
                            }
                        }
                        if ($item['obfs']['type']!= null) {
                            $obfs_type .= "obfs={$item['obfs']['type']}";
                            if ($item['obfs']['param']!= null) {
                                $obfs_param .= "obfs-host={$item['obfs']['param']}";
                            }
                        }
                        $config = [
                            "shadowsocks={$item['host']}:{$item['port']}",
                            "method={$item['cipher']}",
                            "password={$item['password']}",
                            $protocol_type,
                            $protocol_param,
                            $obfs_type,
                            $obfs_param,
                            'fast-open=true',
                            'udp-relay=true',
                            "tag={$item['name']}"
                        ];
                        $config = array_filter($config);
                        $uri .= implode(',', $config). "\r\n";
                    }
                    if ($item['type'] === 'vmess') {
                        $config = [
                            "vmess={$item['host']}:{$item['port']}",
                            "method={$item['cipher']}",
                            "password={$item['password']}",
                            'fast-open=true',
                            'udp-relay=true',
                            "tag={$item['name']}"
                        ];

                        if ($item['tls']) {
                            if ($item['network'] === 'tcp')
                                array_push($config, 'obfs=over-tls');
                            if ($item['tlsSettings']) {
                                $tlsSettings = $item['tlsSettings'];
                                if (isset($tlsSettings['allowInsecure']) && !empty($tlsSettings['allowInsecure']))
                                    array_push($config, 'tls-verification=' . ($tlsSettings['allowInsecure'] ? 'false' : 'true'));
                                if (isset($tlsSettings['serverName']) && !empty($tlsSettings['serverName']))
                                    $host = $tlsSettings['serverName'];
                            }
                        }
                        if ($item['network'] === 'ws') {
                            if ($item['tls'])
                                array_push($config, 'obfs=wss');
                            else
                                array_push($config, 'obfs=ws');
                            if ($item['networkSettings']) {
                                $wsSettings = $item['networkSettings'];
                                if (isset($wsSettings['path']) && !empty($wsSettings['path']))
                                    array_push($config, "obfs-uri={$wsSettings['path']}");
                                if (isset($wsSettings['headers']['Host']) && !empty($wsSettings['headers']['Host']) && !isset($host))
                                    $host = $wsSettings['headers']['Host'];
                            }
                        }
                        if (isset($host)) {
                            array_push($config, "obfs-host={$host}");
                        }

                        $uri .= implode(',', $config) . "\r\n";
                    }
                    if ($item['type'] === 'trojan') {
                        $config = [
                            "trojan={$item['host']}:{$item['port']}",
                            "password={$item['password']}",
                            'over-tls=true',
                            $item['server_name'] ? "tls-host={$item['server_name']}" : "",
                            // Tips: allowInsecure=false = tls-verification=true
                            $item['allow_insecure'] ? 'tls-verification=false' : 'tls-verification=true',
                            'fast-open=true',
                            'udp-relay=true',
                            "tag={$item['name']}"
                        ];
                        $config = array_filter($config);
                        $uri .= implode(',', $config) . "\r\n";
                    }
                }
                return base64_encode($uri);
            break;
            case 'loon':
                $uri = '';
                header("Subscription-Userinfo: expire={$user['expired_at']}");

                foreach ($free as $item) {
                    if ($item['type'] === 'shadowsocks'
                        && in_array($item['cipher'], [
                            'aes-128-gcm',
                            'aes-192-gcm',
                            'aes-256-gcm',
                            'chacha20-ietf-poly1305'
                        ])
                    ) {
                        $config = [
                            "{$item['name']}=Shadowsocks",
                            "{$item['host']}",
                            "{$item['port']}",
                            "{$item['cipher']}",
                            "{$item['password']}",
                            'fast-open=false',
                            'udp=true'
                        ];
                        $config = array_filter($config);
                        $uri .= implode(',', $config) . "\r\n";
                    }
                    if ($item['type'] === 'shadowsocksR'
                        && in_array($item['cipher'], [
                            'aes-128-gcm',
                            'aes-192-gcm',
                            'aes-256-gcm',
                            'aes-128-cfb',
                            'aes-192-cfb',
                            'aes-256-cfb',
                            'aes-128-ctr',
                            'aes-192-ctr',
                            'rc4-md5',
                            'chacha20',
                            'xchacha20',
                            'chacha20-ietf-poly1305',
                            'xchacha20-ietf-poly1305'
                        ])
                    ) {
                        $protocol_type = "";
                        $protocol_param = "";
                        $obfs_type = "";
                        $obfs_param = "";
                        if ($item['protocol']['type']!= null) {
                            $protocol_type .= "protocol={$item['protocol']['type']}";
                            if ($item['protocol']['param']!= null) {
                                $protocol_param .= "protocol-param={$item['protocol']['param']}";
                            }
                        }
                        if ($item['obfs']['type']!= null) {
                            $obfs_type .= "obfs={$item['obfs']['type']}";
                            if ($item['obfs']['param']!= null) {
                                $obfs_param .= "obfs-param={$item['obfs']['param']}";
                            }
                        }
                        $config = [
                            "{$item['name']}=ShadowsocksR",
                            "{$item['host']}",
                            "{$item['port']}",
                            "{$item['cipher']}",
                            "{$item['password']}",
                            $protocol_type,
                            $protocol_param,
                            $obfs_type,
                            $obfs_param,
                            'fast-open=false',
                            'udp=true'
                        ];
                        $config = array_filter($config);
                        $uri .= implode(',', $config) . "\r\n";
                    }
                    if ($item['type'] === 'vmess') {
                        $config = [
                            "{$item['name']}=vmess",
                            "{$item['host']}",
                            "{$item['port']}",
                            "{$item['cipher']}",
                            "{$item['password']}",
                            'fast-open=false',
                            'udp=true',
                            "alterId=".$item['alterId']
                        ];

                        if ($item['network'] === 'tcp') {
                            array_push($config, 'transport=tcp');
                        }
                        if ($item['tls']) {
                            if ($item['network'] === 'tcp')
                                array_push($config, 'over-tls=true');
                            if ($item['tlsSettings']) {
                                $tlsSettings = $item['tlsSettings'];
                                if (isset($tlsSettings['allowInsecure']) && !empty($tlsSettings['allowInsecure']))
                                    array_push($config, 'skip-cert-verify=' . ($tlsSettings['allowInsecure'] ? 'true' : 'false'));
                                if (isset($tlsSettings['serverName']) && !empty($tlsSettings['serverName']))
                                    array_push($config, "tls-name={$tlsSettings['serverName']}");
                            }
                        }
                        if ($item['network'] === 'ws') {
                            array_push($config, 'transport=ws');
                            if ($item['networkSettings']) {
                                $wsSettings = $item['networkSettings'];
                                if (isset($wsSettings['path']) && !empty($wsSettings['path']))
                                    array_push($config, "path={$wsSettings['path']}");
                                if (isset($wsSettings['headers']['Host']) && !empty($wsSettings['headers']['Host']))
                                    array_push($config, "host={$wsSettings['headers']['Host']}");
                            }
                        }
                        $uri .= implode(',', $config) . "\r\n";
                    }
                    if ($item['type'] === 'trojan') {
                        $config = [
                            "{$item['name']}=trojan",
                            "{$item['host']}",
                            "{$item['port']}",
                            "{$item['password']}",
                            $item['server_name'] ? "tls-name={$item['server_name']}" : "",
                            'fast-open=false',
                            'udp=true'
                        ];
                        if (!empty($item['allow_insecure'])) {
                            array_push($config, $item['allow_insecure'] ? 'skip-cert-verify=true' : 'skip-cert-verify=false');
                        }
                        $config = array_filter($config);
                        $uri .= implode(',', $config) . "\r\n";
                    }
                }
                return $uri;
            break;
            case 'surfboard':
                $appName = config('v2board.app_name', 'V2Board')." Free";
                header("content-disposition:attachment;filename*=UTF-8''".rawurlencode($appName).".conf");

                $proxies = '';
                $proxyGroup = '';

                foreach ($free as $item) {
                    if ($item['type'] === 'shadowsocks'
                        && in_array($item['cipher'], [
                            'aes-128-gcm',
                            'aes-192-gcm',
                            'aes-256-gcm',
                            'chacha20-ietf-poly1305'
                        ])
                    ) {
                        // [Proxy]
                        $config = [
                            "{$item['name']}=ss",
                            "{$item['host']}",
                            "{$item['port']}",
                            "encrypt-method={$item['cipher']}",
                            "password={$item['password']}",
                            'tfo=true',
                            'udp-relay=true'
                        ];
                        $config = array_filter($config);
                        $proxies .= implode(',', $config) . "\r\n";
                        // [Proxy Group]
                        $proxyGroup .= $item['name'] . ', ';
                    }
                    if ($item['type'] === 'vmess') {
                        if ($item['alterId'] == 0){
                            $aead = "vmess-aead=true";
                        } else {
                            $aead = "vmess-aead=false";
                        }
                        // [Proxy]
                        $config = [
                            "{$item['name']}=vmess",
                            "{$item['host']}",
                            "{$item['port']}",
                            "username={$item['password']}",
                            "{$aead}",
                            'tfo=true',
                            'udp-relay=true'
                        ];

                        if ($item['tls']) {
                            array_push($config, 'tls=true');
                            if ($item['tlsSettings']) {
                                $tlsSettings = $item['tlsSettings'];
                                if (isset($tlsSettings['allowInsecure']) && !empty($tlsSettings['allowInsecure']))
                                    array_push($config, 'skip-cert-verify=' . ($tlsSettings['allowInsecure'] ? 'true' : 'false'));
                                if (isset($tlsSettings['serverName']) && !empty($tlsSettings['serverName']))
                                    array_push($config, "sni={$tlsSettings['serverName']}");
                            }
                        }
                        if ($item['network'] === 'ws') {
                            array_push($config, 'ws=true');
                            if ($item['networkSettings']) {
                                $wsSettings = $item['networkSettings'];
                                if (isset($wsSettings['path']) && !empty($wsSettings['path']))
                                    array_push($config, "ws-path={$wsSettings['path']}");
                                if (isset($wsSettings['headers']['Host']) && !empty($wsSettings['headers']['Host']))
                                    array_push($config, "ws-headers=Host:{$wsSettings['headers']['Host']}");
                            }
                        }

                        $proxies .= implode(',', $config) . "\r\n";
                        // [Proxy Group]
                        $proxyGroup .= $item['name'] . ', ';
                    }
                    if ($item['type'] === 'trojan') {
                        // [Proxy]
                        $config = [
                            "{$item['name']}=trojan",
                            "{$item['host']}",
                            "{$item['port']}",
                            "password={$item['password']}",
                            $item['server_name'] ? "sni={$item['server_name']}" : "",
                            'tfo=true',
                            'udp-relay=true'
                        ];
                        if (!empty($item['allow_insecure'])) {
                            array_push($config, $item['allow_insecure'] ? 'skip-cert-verify=true' : 'skip-cert-verify=false');
                        }
                        $config = array_filter($config);
                        $proxies .= implode(',', $config) . "\r\n";
                        // [Proxy Group]
                        $proxyGroup .= $item['name'] . ', ';
                    }
                }

                $defaultConfig = base_path() . '/resources/rules/default.surfboard.conf';
                $customConfig = base_path() . '/resources/rules/custom.surfboard.conf';
                if (\File::exists($customConfig)) {
                    $config = file_get_contents("$customConfig");
                } else {
                    $config = file_get_contents("$defaultConfig");
                }

                // Subscription link
                $subsURL = Helper::getSubscribeUrl("/api/v1/client/subscribe?token={$user['token']}&flag=free&client=surge");
                $subsDomain = $_SERVER['HTTP_HOST'];

                $config = str_replace('$subs_link', $subsURL, $config);
                $config = str_replace('$subs_domain', $subsDomain, $config);
                $config = str_replace('$proxies', $proxies, $config);
                $config = str_replace('$proxy_group', rtrim($proxyGroup, ', '), $config);

                $expireDate = $user['expired_at'] === NULL ? '长期有效' : date('Y-m-d H:i:s', $user['expired_at']);
                $subscribeInfo = "title={$appName}订阅信息, content=到期时间：{$expireDate}";
                $config = str_replace('$subscribe_info', $subscribeInfo, $config);

                return $config;
            break;
            case 'v2rayng':
                $uri = '';

                foreach ($free as $item) {
                    if ($item['type'] === 'shadowsocks') {
                        $name = rawurlencode($item['name']);
                        $str = str_replace(
                            ['+', '/', '='],
                            ['-', '_', ''],
                            base64_encode("{$item['cipher']}:{$item['password']}")
                        );
                        $uri .= "ss://{$str}@{$item['host']}:{$item['port']}#{$name}\r\n";
                    }
                    if ($item['type'] === 'vmess') {
                        $config = [
                            "v" => "2",
                            "ps" => $item['name'],
                            "add" => $item['host'],
                            "port" => (string)$item['port'],
                            "id" => $item['password'],
                            "aid" => $item['alterId'],
                            "net" => $item['network'],
                            "type" => $item['cipher'],
                            "host" => "",
                            "path" => "",
                            "tls" => $item['tls'] ? "tls" : "",
                        ];
                        if ($item['tls']) {
                            if ($item['tlsSettings']) {
                                $tlsSettings = $item['tlsSettings'];
                                if (isset($tlsSettings['serverName']) && !empty($tlsSettings['serverName']))
                                    $config['sni'] = $tlsSettings['serverName'];
                            }
                        }
                        if ((string)$item['network'] === 'ws') {
                            $wsSettings = $item['networkSettings'];
                            if (isset($wsSettings['path'])) $config['path'] = $wsSettings['path'];
                            if (isset($wsSettings['headers']['Host'])) $config['host'] = $wsSettings['headers']['Host'];
                        }
                        if ((string)$item['network'] === 'grpc') {
                            $grpcSettings = $item['networkSettings'];
                            if (isset($grpcSettings['serviceName'])) $config['path'] = $grpcSettings['serviceName'];
                        }
                        $uri .= "vmess://" . base64_encode(json_encode($config)) . "\r\n";
                    }
                    if ($item['type'] === 'trojan') {
                        $name = rawurlencode($item['name']);
                        $query = http_build_query([
                            'allowInsecure' => $item['allow_insecure'],
                            'peer' => $item['server_name'],
                            'sni' => $item['server_name']
                        ]);
                        $uri .= "trojan://{$item['password']}@{$item['host']}:{$item['port']}?{$query}#{$name}" . "\r\n";
                    }
                }
                return base64_encode($uri);
            break;
            case 'v2rayn':
                $uri = '';

                foreach ($free as $item) {
                    if ($item['type'] === 'shadowsocks') {
                        if ($item['cipher'] === '2022-blake3-aes-128-gcm') {
                            $serverKey = Helper::getShadowsocksServerKey($item['created_at'], 16);
                            $userKey = Helper::uuidToBase64($item['password'], 16);
                            $password = "{$serverKey}:{$userKey}";
                        } elseif ($item['cipher'] === '2022-blake3-aes-256-gcm') {
                            $serverKey = Helper::getShadowsocksServerKey($item['created_at'], 32);
                            $userKey = Helper::uuidToBase64($item['password'], 32);
                            $password = "{$serverKey}:{$userKey}";
                        } else {
                            $password = $item['password'];
                        }
                        $name = rawurlencode($item['name']);
                        $str = str_replace(
                            ['+', '/', '='],
                            ['-', '_', ''],
                            base64_encode("{$item['cipher']}:{$password}")
                        );
                        $uri .= "ss://{$str}@{$item['host']}:{$item['port']}#{$name}\r\n";
                    }
                    if ($item['type'] === 'vmess') {
                        $config = [
                            "v" => "2",
                            "ps" => $item['name'],
                            "add" => $item['host'],
                            "port" => (string)$item['port'],
                            "id" => $item['password'],
                            "aid" => $item['alterId'],
                            "net" => $item['network'],
                            "type" => $item['cipher'],
                            "host" => "",
                            "path" => "",
                            "tls" => $item['tls'] ? "tls" : "",
                        ];
                        if ($item['tls']) {
                            if ($item['tlsSettings']) {
                                $tlsSettings = $item['tlsSettings'];
                                if (isset($tlsSettings['serverName']) && !empty($tlsSettings['serverName']))
                                    $config['sni'] = $tlsSettings['serverName'];
                            }
                        }
                        if ((string)$item['network'] === 'ws') {
                            $wsSettings = $item['networkSettings'];
                            if (isset($wsSettings['path'])) $config['path'] = $wsSettings['path'];
                            if (isset($wsSettings['headers']['Host'])) $config['host'] = $wsSettings['headers']['Host'];
                        }
                        if ((string)$item['network'] === 'grpc') {
                            $grpcSettings = $item['networkSettings'];
                            if (isset($grpcSettings['serviceName'])) $config['path'] = $grpcSettings['serviceName'];
                        }
                        $uri .= "vmess://" . base64_encode(json_encode($config)) . "\r\n";
                    }
                    if ($item['type'] === 'trojan') {
                        $name = rawurlencode($item['name']);
                        $query = http_build_query([
                            'allowInsecure' => $item['allow_insecure'],
                            'peer' => $item['server_name'],
                            'sni' => $item['server_name']
                            ]);
                            $uri .= "trojan://{$item['password']}@{$item['host']}:{$item['port']}?{$query}#{$name}" . "\r\n";
                    }
                }
                return base64_encode($uri);
            break;
            case 'passwall':
                $uri = '';

                foreach ($free as $item) {
                    if ($item['type'] === 'shadowsocks') {
                        $name = rawurlencode($item['name']);
                        $str = str_replace(
                            ['+', '/', '='],
                            ['-', '_', ''],
                            base64_encode("{$item['cipher']}:{$item['password']}")
                        );
                        $uri .= "ss://{$str}@{$item['host']}:{$item['port']}#{$name}\r\n";
                    }
                    if ($item['type'] === 'shadowsocksR') {
                        $protocol_type = "";
                        $protocol_param = "";
                        $obfs_type = "";
                        $obfs_param = "";
                        if ($item['protocol']['type']!= null) {
                            $protocol_type .= "{$item['protocol']['type']}:";
                            if ($item['protocol']['param']!= null) {
                                $protocol_param .= "protoparam=".base64_encode("{$item['protocol']['param']}")."&";
                            }
                        }
                        if ($item['obfs']['type']!= null) {
                            $obfs_type .= "{$item['obfs']['type']}:";
                            if ($item['obfs']['param']!= null) {
                                $obfs_param .= "obfsparam=".base64_encode("{$item['obfs']['param']}")."&";
                            }
                        }
                        $uri .= "ssr://".base64_encode("{$item['host']}:{$item['port']}:".$protocol_type."{$item['cipher']}:".$obfs_type.base64_encode("{$item['password']}")."/?".$obfs_param.$protocol_param."remarks=".base64_encode("{$item['name']}")."\r\n");
                    }
                    if ($item['type'] === 'vmess') {
                        $config = [
                            "v" => "2",
                            "ps" => $item['name'],
                            "add" => $item['host'],
                            "port" => (string)$item['port'],
                            "id" => $item['password'],
                            "aid" => $item['alterId'],
                            "net" => $item['network'],
                            "type" => $item['cipher'],
                            "host" => "",
                            "path" => "",
                            "tls" => $item['tls'] ? "tls" : "",
                        ];
                        if ($item['tls']) {
                            if ($item['tlsSettings']) {
                                $tlsSettings = $item['tlsSettings'];
                                if (isset($tlsSettings['serverName']) && !empty($tlsSettings['serverName']))
                                    $config['sni'] = $tlsSettings['serverName'];
                            }
                        }
                        if ((string)$item['network'] === 'ws') {
                            $wsSettings = $item['networkSettings'];
                            if (isset($wsSettings['path'])) $config['path'] = $wsSettings['path'];
                            if (isset($wsSettings['headers']['Host'])) $config['host'] = $wsSettings['headers']['Host'];
                        }
                        if ((string)$item['network'] === 'grpc') {
                            $grpcSettings = $item['networkSettings'];
                            if (isset($grpcSettings['serviceName'])) $config['path'] = $grpcSettings['serviceName'];
                        }
                        $uri .= "vmess://" . base64_encode(json_encode($config)) . "\r\n";
                    }
                    if ($item['type'] === 'trojan') {
                        $name = rawurlencode($item['name']);
                        $query = http_build_query([
                            'allowInsecure' => $item['allow_insecure'],
                            'peer' => $item['server_name'],
                            'sni' => $item['server_name']
                        ]);
                        $uri .= "trojan://{$item['password']}@{$item['host']}:{$item['port']}?{$query}#{$name}" . "\r\n";
                    }
                }
                return base64_encode($uri);
            break;
            case 'ssrplus':
                $uri = '';
                
                foreach ($free as $item) {
                    if ($item['type'] === 'shadowsocks') {
                        $name = rawurlencode($item['name']);
                        $str = str_replace(
                            ['+', '/', '='],
                            ['-', '_', ''],
                            base64_encode("{$item['cipher']}:{$item['password']}")
                        );
                        $uri .= "ss://{$str}@{$item['host']}:{$item['port']}#{$name}\r\n";
                    }
                    if ($item['type'] === 'shadowsocksR') {
                        $protocol_type = "";
                        $protocol_param = "";
                        $obfs_type = "";
                        $obfs_param = "";
                        if ($item['protocol']['type']!= null) {
                            $protocol_type .= "{$item['protocol']['type']}:";
                            if ($item['protocol']['param']!= null) {
                                $protocol_param .= "protoparam=".base64_encode("{$item['protocol']['param']}")."&";
                            }
                        }
                        if ($item['obfs']['type']!= null) {
                            $obfs_type .= "{$item['obfs']['type']}:";
                            if ($item['obfs']['param']!= null) {
                                $obfs_param .= "obfsparam=".base64_encode("{$item['obfs']['param']}")."&";
                            }
                        }
                        $uri .= "ssr://".base64_encode("{$item['host']}:{$item['port']}:".$protocol_type."{$item['cipher']}:".$obfs_type.base64_encode("{$item['password']}")."/?".$obfs_param.$protocol_param."remarks=".base64_encode("{$item['name']}")."\r\n");
                    }
                    if ($item['type'] === 'vmess') {
                        $config = [
                            "v" => "2",
                            "ps" => $item['name'],
                            "add" => $item['host'],
                            "port" => (string)$item['port'],
                            "id" => $item['password'],
                            "aid" => $item['alterId'],
                            "net" => $item['network'],
                            "type" => $item['cipher'],
                            "host" => "",
                            "path" => "",
                            "tls" => $item['tls'] ? "tls" : "",
                        ];
                        if ($item['tls']) {
                            if ($item['tlsSettings']) {
                                $tlsSettings = $item['tlsSettings'];
                                if (isset($tlsSettings['serverName']) && !empty($tlsSettings['serverName']))
                                    $config['sni'] = $tlsSettings['serverName'];
                            }
                        }
                        if ((string)$item['network'] === 'ws') {
                            $wsSettings = $item['networkSettings'];
                            if (isset($wsSettings['path'])) $config['path'] = $wsSettings['path'];
                            if (isset($wsSettings['headers']['Host'])) $config['host'] = $wsSettings['headers']['Host'];
                        }
                        if ((string)$item['network'] === 'grpc') {
                            $grpcSettings = $item['networkSettings'];
                            if (isset($grpcSettings['serviceName'])) $config['path'] = $grpcSettings['serviceName'];
                        }
                        $uri .= "vmess://" . base64_encode(json_encode($config)) . "\r\n";
                    }
                    if ($item['type'] === 'trojan') {
                        $name = rawurlencode($item['name']);
                        $query = http_build_query([
                            'allowInsecure' => $item['allow_insecure'],
                            'peer' => $item['server_name'],
                            'sni' => $item['server_name']
                        ]);
                        $uri .= "trojan://{$item['password']}@{$item['host']}:{$item['port']}?{$query}#{$name}" . "\r\n";
                    }
                }
                return base64_encode($uri);
            break;
            case 'shadowsocks':
                $configs = [];
                $subs = [];
                $subs['servers'] = [];

                $nodeid = 0;
                foreach ($free as $item) {
                    if ($item['type'] === 'shadowsocks'
                        && in_array($item['cipher'], ['aes-128-gcm', 'aes-256-gcm', 'aes-192-gcm', 'chacha20-ietf-poly1305'])
                    ) {
                        $config = [
                            "id" => $nodeid,
                            "remarks" => $item['name'],
                            "server" => $item['host'],
                            "server_port" => $item['port'],
                            "password" => $item['password'],
                            "method" => $item['cipher']
                        ];
                        array_push($configs, $config);
                    }
                    $nodeid++;
                }

                $subs['version'] = 1;
                $subs['servers'] = array_merge($subs['servers'] ? $subs['servers'] : [], $configs);

                return json_encode($subs, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT);
            break;
        }
    }
}

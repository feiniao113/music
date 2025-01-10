<?php
// 获取 URL 参数
if (isset($_GET['url']) && !empty($_GET['url'])) {
    $downloadUrl = $_GET['url'];

    // 获取文件名
    $filename = basename(parse_url($downloadUrl, PHP_URL_PATH));

    // 指定默认保存路径
    $baseSavePath = '/www/wwwroot/189.1.224.165_96/tv/';
    $savePath = $baseSavePath . $filename;

 
    $fileContent = file_get_contents($downloadUrl);

  
    if ($fileContent === FALSE) {
        echo "下载失败，请检查链接或网络连接。";
    } else {
        
        $fileSaved = file_put_contents($savePath, $fileContent);

       
        if ($fileSaved !== FALSE) {
            echo "文件已成功下载并保存为：$savePath\n";

           
            $extractPath = $baseSavePath; 
            if (strpos($downloadUrl, '补丁') !== FALSE) {
                
                $extractPath .= 'TVBoxOSC/';
            } elseif (strpos($downloadUrl, '单线路') !== FALSE) {
          
            }

     
            $zip = new ZipArchive();
            if ($zip->open($savePath) === TRUE) {
  
                $zip->extractTo($extractPath);
                $zip->close();

                echo "文件解压成功！解压路径：$extractPath\n";

                
                if (strpos($downloadUrl, '单线路') !== FALSE) {
                   
                    $tvBoxFolder = $extractPath . 'TVBoxOSC/';
                    if (is_dir($tvBoxFolder)) {
                      
                        $tvBoxSubFolder = $tvBoxFolder . 'tvbox/';
                        $apiFile = $tvBoxSubFolder . 'api.json';

                        if (file_exists($apiFile)) {
                      
                            $apiContent = file_get_contents($apiFile);
                            if ($apiContent !== FALSE) {
                             
                                $apiData = json_decode($apiContent, true);

                               
                                if (isset($apiData['sites'])) {
                                    foreach ($apiData['sites'] as &$site) {
                                        // 判断是否为 "检查更新"
                                        if (isset($site['key']) && $site['key'] === '检查更新') {
                                            // 添加新的字段 "ext" 和 "jar"
                                            $site['ext'] = 'http://域名/tvjiami/get.php?url=http://域名/tvjiami/dizhi.php';
                                            $site['jar'] = 'http://域名/tvjiami/spider.jar';
                                
                                            echo "修改成功：检查更新的 ext 和 jar 字段已添加。\n";
                                        }
                                    }
                                }


                                
                                file_put_contents($apiFile, json_encode($apiData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
                                echo "api.json 文件已成功修改！路径：$apiFile\n";
                            } else {
                                echo "无法读取 api.json 文件内容。\n";
                            }
                        } else {
                            echo "api.json 文件未找到。\n";
                        }
                    } else {
                        echo "TVBoxOSC 文件夹未找到。\n";
                    }
                }
            } else {
                echo "文件解压失败！";
            }

            // 可选：下载后的压缩包可以删除
            // unlink($savePath); // 如果需要，可以删除已下载的压缩包
        } else {
            echo "文件保存失败。";
        }
    }
} else {
    echo "未提供下载链接，请使用 url 参数。";
}
?>

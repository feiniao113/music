<?php

if (isset($_GET['url'])) {
    $url = $_GET['url'];
    
    // 初始化cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    $response = curl_exec($ch);


    curl_close($ch);
    
    // 检查响应
    if ($response) {
        $data = json_decode($response, true); 

        // 基础的URL前缀
        $base_url = 'https://github.com/feiniao113/music/blob/main/xiaosa/xiazai.php?url=';

        // 修改URL
        foreach ($data as &$category) {
            if (isset($category['list'])) {
                foreach ($category['list'] as &$item) {
                    if (isset($item['url'])) {
                        // 直接拼接URL，不使用urlencode
                        $item['url'] = $base_url . $item['url'];
                    }
                }
            }
        }

        // 返回JSON数据
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    } else {
        // 错误处理
        echo json_encode(['error' => 'Unable to fetch data']);
    }
} else {
    // 未提供URL参数的错误处理
    echo json_encode(['error' => 'No URL parameter provided']);
}
?>

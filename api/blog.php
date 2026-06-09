<?php
// api/blog.php

require_once __DIR__ . '/BaseEasonAPI.php';

class EasonSaidBlogAPI extends BaseEasonAPI {
    
    /**
     * 格式化输出内容（HTML格式）
     */
    public function formatOutput($record) {
        if (!$record || !isset($record['Contents'])) {
            return "暂时没有找到分享内容。";
        }
        
        $contents = htmlspecialchars($record['Contents'] ?: '', ENT_QUOTES, 'UTF-8');
        $source = htmlspecialchars($record['Source'] ?: '未知出处', ENT_QUOTES, 'UTF-8');
        
        $output = "<div class='cn'>{$contents}</div><div class='en'>《{$source}》</div>";
        
        return $output;
    }
    
    /**
     * 输出HTML格式
     */
    private function outputText($record) {
        header('Content-Type: text/html; charset=utf-8');
        echo $this->formatOutput($record);
    }
    
    /**
     * 主执行函数
     */
    public function run() {
        // 获取随机记录
        $record = $this->getRandomRecord();
        
        if ($record['success']) {
            $this->outputText($record['data']);
        } else {
            $this->outputText(null);
        }
    }
}

// 执行程序
$api = new EasonSaidBlogAPI();
$api->run();

?>
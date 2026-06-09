<?php
// api/output.php

require_once __DIR__ . '/BaseEasonAPI.php';

class EasonSaidOutputAPI extends BaseEasonAPI {
    
    /**
     * 格式化输出内容
     */
    public function formatOutput($record) {
        if (!$record || !isset($record['Contents'])) {
            return "暂时没有找到分享内容。";
        }
        
        $contributers = $record['Contributors'] ?: '匿名';
        $contents = $record['Contents'] ?: '';
        $source = $record['Source'] ?: '未知出处';
        
        $output = "\u{201c}{$contributers}\u{201d} 向你分享：\u{201c}{$contents}\u{201d}。它出自：《{$source}》。";
        
        return $output;
    }
    
    /**
     * 输出文本格式
     */
    private function outputText($record) {
        header('Content-Type: text/plain; charset=utf-8');
        echo $this->formatOutput($record);
    }
    
    /**
     * 输出JSON格式
     */
    private function outputJSON($record) {
        header('Content-Type: application/json; charset=utf-8');
        
        if ($record['success']) {
            $response = [
                'success' => true,
                'data' => $record['data'],
                'formatted' => $this->formatOutput($record['data'])
            ];
        } else {
            $response = [
                'success' => false,
                'message' => $record['message']
            ];
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    
    /**
     * 主执行函数
     */
    public function run() {
        // 获取output参数，默认为text
        $outputType = isset($_GET['output']) ? strtolower($_GET['output']) : 'text';
        
        // 验证output参数 - 使用JSON形式返回错误
        if (!in_array($outputType, ['text', 'json'])) {
            $this->handleError('参数错误：output只能为text或json', 'json');
        }
        
        // 获取随机记录
        $record = $this->getRandomRecord();
        
        // 根据output参数选择输出格式
        if ($outputType === 'json') {
            $this->outputJSON($record);
        } else {
            if ($record['success']) {
                $this->outputText($record['data']);
            } else {
                $this->outputText(null);
            }
        }
    }
}

// 执行程序
$api = new EasonSaidOutputAPI();
$api->run();

?>
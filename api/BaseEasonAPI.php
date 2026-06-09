<?php
// api/BaseEasonAPI.php - 基础 API 类，提供数据库连接和通用方法

class BaseEasonAPI {
    protected $pdo;
    
    public function __construct() {
        // 设置CORS头，允许前端跨域访问
        $this->setCorsHeaders();
        
        // 处理预检请求（OPTIONS）
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
        
        try {
            $config = require __DIR__ . '/config.php';
            $this->pdo = new PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4",
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            error_log('数据库连接失败: ' . $e->getMessage());
            $this->handleError('服务器内部错误，请稍后再试');
        }
    }
    
    /**
     * 从eason_said_with_counts视图中随机获取一条记录
     */
    public function getRandomRecord() {
        try {
            // 使用RAND()函数随机排序并限制返回1条记录
            $sql = "SELECT * FROM eason_said_with_counts ORDER BY RAND() LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            $result = $stmt->fetch();
            
            if ($result) {
                return [
                    'success' => true,
                    'data' => $result
                ];
            } else {
                return [
                    'success' => false,
                    'message' => '没有找到相关记录'
                ];
            }
            
        } catch (PDOException $e) {
            error_log('查询失败: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => '服务器内部错误，请稍后再试'
            ];
        }
    }
    
    /**
     * 处理错误信息
     */
    protected function handleError($message, $outputType = 'json') {
        if ($outputType === 'json') {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'error' => true,
                'message' => $message
            ], JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: text/plain; charset=utf-8');
            echo "错误: " . $message;
        }
        exit;
    }
    
    /**
     * 设置CORS响应头，允许前端跨域访问
     */
    protected function setCorsHeaders() {
        $allowedOrigins = [
            'https://easonsaid.cn',
            'https://www.easonsaid.cn',
            'http://easonsaid.cn',
            'http://www.easonsaid.cn'
        ];
        
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        
        if (in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
        } else {
            // 对于没有Origin头的请求（如直接访问），也允许
            header("Access-Control-Allow-Origin: https://easonsaid.cn");
        }
        
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");
        header("Access-Control-Max-Age: 86400");
    }
}

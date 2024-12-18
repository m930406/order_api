1. 資料庫測驗
   1. 第一題
        SELECT 
            o.bnb_id, 
            b.name AS bnb_name, 
            SUM(o.amount) AS may_amount
        FROM 
            orders o
        JOIN 
            bnbs b ON o.bnb_id = b.id
        WHERE 
            o.currency = 'TWD' 
            AND o.created_at >= '2023-05-01' 
            AND o.created_at < '2023-06-01'
        GROUP BY 
            o.bnb_id, b.name
        ORDER BY 
            may_amount DESC
        LIMIT 10;
    2. 第二題
            先用 explain 查看目前語法狀況, 確認是否使用到索引
            現狀應沒有, 以需求的語法來說, 應該以 orders 的 created_at, currency, bnb_id 建立複合索引
            那未來若是有固定需要每個月統計, 還可以用 created_at 以各個月份為範圍來切 partition
            若效能還是無法改善, 可能要考慮以ETL或是背景方式, 預先統計好數據儲存在另一位子, 需要時直接撈取

2. API 實作測驗
   1. 請見此專案程式碼
   2. 進度交代: 有完成 API 相關邏輯, 且使用 docker 環境, 並透過 wait-for-it.sh 達成 db contaimer 完整啟動後, 才觸發 migration
   3. Feature 及 Unit 兩種測試, 皆未完成, 卡在環境建置, 目前以私人windows電腦搭配VB架設環境, 碰到許多問題, 在評估時間後, 只能放棄
   
3. 架構測驗
   1. 服務指標
      1. 高可用性, 且能承受大流量, 隨使用者規模擴展服務
      2. 安全性, 避免帳戶訊息資料洩漏
      3. 跨平台支援, iOS, Andriod, WEB
   2. 端口
      1. 用戶端
      2. 管理端
   3. 服務組成
      1. 使用 GCP
      2. 用戶端
         1. iOS / Andriod APP
            1. 使用 Flutter 開發
         2. WEB Frontend
            1. 使用 Vue
         3. Backend Service
            1. 使用 Golang
      3. 管理端
         1. WEB Fronted
            1. 使用 Vue
         2. Backend Service
            1. 使用 Golang
               1. 透過WebSocket方式提供訊息服務
      4. 資料庫
         1. 使用 MySQL 儲存會員資料及訊息紀錄
         2. 使用 Redis 進行快取及即時資料存取
      5. 上傳文件/圖片儲存空間
         1. 使用 GCS 存放
   4. 功能設計
      1. 註冊/登入
         1. 信箱註冊
         2. 第三方登入(Google, Line等)
      2. 會員中心
         1. 資料管理
         2. 頭像上傳
         3. 查看他人基本資料
      3. 聯絡人
         1. 新增 / 封鎖 / 刪除
         2. 新增 請求/接受
      4. 訊息
         1. 一對一
         2. 群組
         3. 文字 / 圖片 / 檔案
         4. 點對點加密
      5. 訊息通知
         1. 推播通知
   5. 部署方式
      1. Github Actions
   6. Log 查詢/監控
      1. Datadog
         1. 建立相關指標, 透過 Datadog 功能監控是否有異常, 並於異常時主動以 TG 或是其他方式示警
   7. 備份
      1. 會員資料
         1. 定期將資料庫備份
      2. 訊息資料
         1. 透過資料分區方式, 定時從線上資料庫將資料轉移到離線資料庫, 減低線上資料量大小, 也保有備援資料
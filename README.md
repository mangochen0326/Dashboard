# Dashboard

使用armchart map 物件顯示資料表所抓取出來的資料顯示於世界地圖上
並透過Node.js + Socket.io 監聽資料表當資料異動時廣播出去Client端
資料監聽的方式透過Sql Server Trigger After insert,update,delte 呼叫WebService

![alt tag](https://raw.githubusercontent.com/mangochen0326/Dashboard/master/images/sample.png)

# A SWoole for Yaf Http Server

## 安装
```
    composer require whero/swf-server
```
## 启用
```
  php bin/swf start -d
```


##  压力测试
```
  ab -c 100 -n 1000000 -k http://127.0.0.1:9501/
```
```
Benchmarking 127.0.0.1 (be patient)
Completed 100000 requests
Completed 200000 requests
Completed 300000 requests
Completed 400000 requests
Completed 500000 requests
Completed 600000 requests
Completed 700000 requests
Completed 800000 requests
Completed 900000 requests
Completed 1000000 requests
Finished 1000000 requests


Server Software:        swoole-http-server
Server Hostname:        127.0.0.1
Server Port:            9501

Document Path:          /
Document Length:        18 bytes

Concurrency Level:      100
Time taken for tests:   12.012 seconds
Complete requests:      1000000
Failed requests:        0
Keep-Alive requests:    1000000
Total transferred:      193000000 bytes
HTML transferred:       18000000 bytes
Requests per second:    83250.89 [#/sec] (mean)
Time per request:       1.201 [ms] (mean)
Time per request:       0.012 [ms] (mean, across all concurrent requests)
Transfer rate:          15690.84 [Kbytes/sec] received
```

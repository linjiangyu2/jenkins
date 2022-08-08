<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2016091000479921",

		//商户私钥
		'merchant_private_key' => "MIIEowIBAAKCAQEAqk3Tl4+/1NahKKRXwA5Ntc0l7cP/oOkZILdsGr16fsbQa1SpqYrN+6T70UMc3420qmAfjBwfB5GJG+ekbE3agIKV+OSIny8ZIupcWCkSxgHSg4PUo2a1zF1BqIdrWSukqeZgGvCOFTXLBLY9pD/X1CWWOjgUKouCgi5LauAQQpiq30NgDaGrcefkL9LEJcjbzZGTw9NGPIDnNeBcPwK1ZyQ1EaEeu8muwq8qKB9vP1Ym4SzCuigaQXxCfcohdu9kaZ3eJQoN/QvNd1rp2qsVvbtQidVlSAOC/Lb1hizYbruGkP4/r8Jhqf1k2COKWmlNIZtfmD9XNMzks3yiVvzlnwIDAQABAoIBAEiaIyFSzAINeejcnvgmYOSnT4scaocLuIWlDHYd3vHbChEnp6i6nvOvsxbUGKQJQkL79ZcCigvs9FmsGAF/8y2j/hF8Q+4w4vNqm/GNmQ5Gypr8gZMjf09fVlVlXdNG8sznhIMXmErcgu9ATekOupEbcP7i114H2Zxr5gTq/qOUxgCpydkl6RD6S7iFp0CW9JEYkbj61Dv80mi/MtEFDFyiM8pP3ak4SE5uM4qqrpLDHJB/hjy4lluIMRhaXiaO00iYhMEh0dIFCIcLoHIDCzMpSwzM/8nDf4klpE7SSeC7v6E8c7hAXGJ2WuoEBgZD1F/3RTG+hCXqpZqKU0kJTLECgYEA01MzpUCqyzYb0SQOBUKpi68U4l6Cxq4gE/e+EuCX+h6h2kCuJuijlqbo0n25wE7qzSYMXsT6UktLhjFR+2vSs73u1JB9gIc5ZK1kyYCsIOwlY7bbIJIYzjXdcD6TY7XKZSiPm3CIHDpuZeMhfE7Ri9xFt9wtFFaLL6fWzLwHcdkCgYEAzk6XCqCxG7XhFma3GrJIV7bv00+9eD70UxhxEpBiDQcU3UWO62Pbk8p1kowOm10Aatwe+1f0pBtCEzP7ZsB9g9xQCLs4VBDZVy+457N+bXIA9pTogWXsO82lmZSLF2YfY+cUdibMfq1yVyCh/dMPPc35SuM+urbB9FTU9dro8DcCgYEApXJVU8KONOyW2KVeqLWpIaggNJ4DyuLWUGu8jvDxaywCUWokLmgiczcXvnwaKjpez+BC/QtAY595cIU6hxnCa2B/FEJPT4oO4Ah0iOHJYTkgiLHHWvemngND67XCFOVloM8hp3NxzI/ekLxDIhxfKB17I9goHu0mqVfA7qcjOPkCgYBM0CQal+P5bkmlPo80SGb5CarXoZvo9n+fpL0M7WckdJuHG0vwRpjNuRb3fmA95m2uW9DJQmFa7K84WSVkh4z0GIZQCe0aF7/kX2dYFZOgCk9jf+Plsd44EgRzX0Q+bQ6I8tPCfgWrsMaevA60Y+4L0/HauSt093DVmPRFqcELFwKBgAPlL1ddESad4wu82LfHKK6PQ/5sMhnzvjjYe+dPQ7OJI+tm2FYKJpETDs7S3kFXGxI6zdNhxfFkROFc0B633Q+i/C7dpJyANruLZnYlQA1MpGE7MkLdt42FtsQroEjHkdWkXgDTPn8/MIg7Lt9vmERBaHyGCodKJTexdismmwW1",
		
		//异步通知地址
		'notify_url' => "http://" . $_SERVER['SERVER_NAME'] . "/index.php/home/order/notify",
		
		//同步跳转
		'return_url' => "http://" . $_SERVER['SERVER_NAME'] . "/index.php/home/order/callback",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzJ8L6mG0l8VddLtuGG1sX7vLJyvXKmmE4p6k2oGcUjmPBJMstnTkgSd/qnFF6lx1I8aUmeMo9+S4yYtooVTVexswk2nNTKc3HaXkvb1ftx8PyljJ4HvQI6qACMnrFMuoFY5tqc0WInbKWL2Ueszi5Fvzj76VOMbXu+F+oyG7pd+8PFXX5G07gOqobq23rC6BzAn5Y+e+YYjz0nMrI5VmpklSaoRtpRDR27ZhV9gbIUZ4Zi2sKzJlK6aRJalLSj8ELCrXaSAAkzt15T1zcsKPDQ+94gG83flxLamwk9EoKum6ELbd6xA7NxfeGVyOs0IzDrLGh50kW0ddiI/Fyq2G7QIDAQAB",
);
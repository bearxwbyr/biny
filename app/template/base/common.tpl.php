<?php
/* @var $this TXResponse */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8;"/>
    <meta name="renderer" content="webkit">
    <META http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width,user-scalable=yes">
    <meta name="keywords" content="<?=$this->htmlEncode($this->keywords) ?: "PHP框架"?>">
    <meta name="description" content="<?=$this->htmlEncode($this->descript) ?: "一款轻量级的PHP框架，兼容各种模式的web架构。"?>">

    <title><?=$this->htmlEncode($this->title) ?: "Biny"?></title>
    <link rel="icon" href="<?=$rootPath?>static/images/icon/favicon.ico" type="image/x-icon" />

    <link href="<?=$rootPath?>static/css/main.css" rel="stylesheet" type="text/css"/>

</head>
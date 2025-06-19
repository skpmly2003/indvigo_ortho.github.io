<?php

if (isset($_POST['question'])) {$phone = $_POST['question'];}
if (isset($_POST['first-name'])) {$name = $_POST['first-name'];}
if (isset($_POST['last-name'])) {$name = $_POST['last-name'];}
if (isset($_POST['email'])) {$name = $_POST['email'];}
if (isset($_POST['tel'])) {$name = $_POST['tel'];}
 
/* Сюда впишите свою эл. почту */
$myaddres  = "konchits_2003@mail.ru"; // кому отправляем
 
/* А здесь прописывается текст сообщения, \n - перенос строки */
$mes = "Тема: Заказ обратного звонка!\nТелефон: $phone\nИмя: $name";
 

$sub='Заказ с сайта'; 
$email='Заказ обратного звонка'; 
$send = mail ($myaddres,$sub,$mes,"Content-type:text/plain; charset = utf-8\r\nFrom:$email");
 
?>
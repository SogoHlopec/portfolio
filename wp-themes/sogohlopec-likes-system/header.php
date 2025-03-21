<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo wp_get_document_title(); ?></title>
    <?php wp_head() ?>
</head>

<body>
    <div class="wrapper">
        <header class="header">
            <div class="container">
                <p class="title header__title">Header</p>
            </div>
            <nav class="header__menu menu">
                <div class="container">
                    <ul class="menu__list">
                        <li><a href="#">Главная</a></li>
                        <li><a href="#">Статьи</a></li>
                        <li><a href="#">Новости</a></li>
                    </ul>
                </div>
            </nav>
        </header>
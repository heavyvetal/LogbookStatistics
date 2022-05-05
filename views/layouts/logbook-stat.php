<?php
use yii\helpers\Html;
use app\assets\AppAsset;

AppAsset::register($this);

$this->beginPage() ?>
<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="shortcut icon" href="img/favicon.ico">
    <script src="js/jquery-3.4.1.min.js" type="text/javascript"></script>
    <script src="js/main.js" type="text/javascript"></script>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<header>
    <div class="container">
        <div class="row d-flex justify-content-between align-items-center">
            <div class="cols-md-4 title">
                LogBookStat
            </div>
            <div class="cols-md-4 top-nav">
            </div>
        </div>
    </div>
</header>
<div id="section1">
    <div class="container">
        <div class="group-spec">
            <form>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <select id="group" class="form-control">
                            <option selected>Выберите группу...</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <select id="spec" class="form-control">
                            <option selected>Выберите предмет...</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <button type="submit" id="get-stat" class="btn btn-primary">Получить статистику</button>
                    </div>
                </div>
            </form>
        </div>
        <div id="start-info">
            Для авторизации используйте ваши доступы от сайта logbook.itstep.org. Чтобы войти в демонстрационный аккаунт, введите login: test, password: test. Демо-версия имеет ограниченные возможности. В ней данные доступны только для группы «В2811» и только для 3 предметов: "IT-Старт ВШ", "3D-моделирование и 3D печать 2020 ВШ", "Программирование на Python - Junior 2020 (ВШ)".
        </div>
        <div class="table-stat">
            <table class="zebra">
            </table>
        </div>
        <div id="info">
            *Средняя оценка по текущим рассчитывается, как среднее арифметическое по всем доступным.<br>
            *Во время расчёта средней оценки по всем возможным учитываются также прочерки. Они заменяются оценкой "1".
        </div>
    </div>
</div>
<footer>
    <div class="container">
        <div class="row d-flex justify-content-between align-items-center">
            <div class="col-md-4 copy">
                2019 &copy Step Web Studio
            </div>
        </div>
    </div>
</footer>
<div id="overlay">
    <div class="wrapper">
        <img src="img/preloader.png" alt="">
        <div class="connection-label">Идет соединение с сервером...</div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
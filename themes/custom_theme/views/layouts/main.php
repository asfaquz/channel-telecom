<?php $this->renderPartial('//layouts/_header', array('data' => '')); ?>
<div id="wrapper">

    <header id="header" class="clearfix" role="banner">

        <hgroup>
            <h1 id="site-title"><a href="#"><?php echo CHtml::encode($this->pageTitle); ?></a></h1>
            <!--            <h2 id="site-description">YII Test App!</h2>-->
        </hgroup>

    </header> <!-- #header -->

    <div id="main" class="clearfix">

        <!-- Navigation -->
        <nav id="menu" class="clearfix" role="navigation">
            <div id="mainmenu">
                <?php
                $this->widget('zii.widgets.CMenu', array(
                    'items' => array(
                        array('label' => 'Home', 'url' => array('/site/index')),
                    ),
                ));
                ?>
            </div><!-- mainmenu -->
        </nav> <!-- #nav -->

        <!-- Show a "Please Upgrade" box to both IE7 and IE6 users (Edit to IE 6 if you just want to show it to IE6 users) - jQuery will load the content from js/ie.html into the div -->

        <!--[if lte IE 7]>
            <div class="ie warning"></div>
        <![endif]-->

        <div id="content" role="main">
            <?php echo $content; ?>

            <hr /> <!-- Post seperator - Not the most optimal solution -->

            <article class="post">

        </div> <!-- #content -->

        <aside id="sidebar" role="complementary">

            <aside class="widget">
                <h3>Sidebar heading</h3>

                <?php
                $this->widget('zii.widgets.CMenu', array(
                    'items' => array(
                        array('label' => 'Home', 'url' => array('/site/index')),
                        array('label' => 'TEST API', 'url' => array('/site/TestApi')),
                    ),
                ));
                ?>
            </aside> <!-- .widget -->
    </div> <!-- #main -->
    <?php $this->renderPartial('//layouts/_footer', array('data' => '')); ?>
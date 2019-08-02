<?php

include_once 'Product.php';

class TextProduct implements Product
{
    private $mfgProduct;

    public function getProperties()
    {
        $this->mfgProduct = <<<MALI
<!DOCTYPE html><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style type="text/css">
    header {
        color: #900;
        font-weight: bold;
        font-size: 24px;
        font-family: Verdana, Geneva, sans-serif;
    }
    p {
        font-family: Verdana, Geneva, sans-serif;
        font-size: 12px;
    }
    </style>
    <title>Mali</title>
</head>
<body>
    <header>Mali</header>
    <p>Mali was once part of three famed West African empires which controlled trans-Saharan trade in gold, salt, slaves, and other precious commodities. These Sahelian kingdoms had neither rigid geopolitical boundaries nor rigid ethnic identities. The earliest of these empires was the Ghana Empire, which was dominated by the Soninke, a Mande-speaking people. The empire expanded throughout West Africa from the 8th century until 1078, when it was conquered by the Almoravids.</p>

    <p>The Mali Empire later formed on the upper Niger River, and reached the height of power in the 14th century. Under the Mali Empire, the ancient cities of Djenné and Timbuktu were centers of both trade and Islamic learning. The empire later declined as a result of internal intrigue, ultimately being supplanted by the Songhai Empire. The Songhai people originated in current northwestern Nigeria. The Songhai had long been a major power in West Africa subject to the Mali Empire's rule.</p>
    
    <p>In the late 14th century, the Songhai gradually gained independence from the Mali Empire and expanded, ultimately subsuming the entire eastern portion of the Mali Empire. The Songhai Empire's eventual collapse was largely the result of a Moroccan invasion in 1591, under the command of Judar Pasha. The fall of the Songhai Empire marked the end of the region's role as a trading crossroads. Following the establishment of sea routes by the European powers, the trans-Saharan trade routes lost significance.</p>
</body>
</html>
MALI;

        return $this->mfgProduct;
    }
}

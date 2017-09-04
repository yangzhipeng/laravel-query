<?php

namespace App\Http\Controllers;

use DB;
use QL\QueryList;

class QueryController extends Controller
{
    public function query()
    {
        //待采集的目标页面
        // $pn = $page;
        //采集规则
        // $url   = "https://www.amazon.com/s/ref=sr_in_-2_p_89_1?fst=as%3Aoff&rh=n%3A7141123011%2Cn%3A7147440011%2Cn%3A1040660%2Cn%3A9522931011%2Cn%3A14333511%2Cn%3A1044960%2Ck%3Asports+bra%2Cp_89%3AAM+CLOTHES&bbn=1044960&keywords=sports+bra&ie=UTF8&qid=1502416854&rnid=2528832011";
        // $rules = array(
        //     'title'   => array('div.a-spacing-none>div.a-spacing-micro>a>h2.a-size-small', 'text'),
        //     'price'   => array('div.a-spacing-none>a.a-link-normal>span', 'text'),
        //     'link' => array('div.a-spacing-none>div.a-spacing-micro>a','href','',function($link){
        //          return  QueryList::Query($link,array(
        //             'comment' => array('div#averageCustomerReviews_feature_div>div#averageCustomerReviews>span.a-declarative>a#acrCustomerReviewLink>span', 'text'),
        //     'price' => array('div#unifiedPrice_feature_div>div#price>table.a-lineitem>td.a-span12>span','text'),
        //  ))->data;

        // }),
        // );
        //列表选择器
        // $rang = 'li>div.s-item-container';
        //采集
        // $data = \QL\QueryList::Query($url, $rules, $rang)->data;
        //查看采集结果
        // $i = 0;
        // foreach ($data as $key => $value) {
        //     $title   = (string) $value['title'];
        //     $price   = str_replace(' ', '', trim($value['price']));
        //     $price   = str_replace("\n", '', $price);
        //     // $comment = trim($value['comment']);
        //     $content = "商品名称：" . $title . "   价格：" . $price . "\r\n";

        //     $file = '/home/halo/working/query/QueryList/file.txt';
        //     if ($f = file_put_contents($file, $content, FILE_APPEND)) {
        // // 这个函数支持版本(PHP 5)
        //         $i++;
        //         echo "写入成功。\n" . $i;
        //     }
        // }
        // print_r($data);

        $hj = QueryList::Query("https://www.amazon.com/s/ref=sr_pg_16?fst=as%3Aoff&rh=k%3Abra%2Cn%3A7141123011%2Cn%3A7147440011%2Cn%3A1040660%2Cp_89%3APlaytex&page=16&bbn=1040660&keywords=bra&ie=UTF8&qid=1502766969", array(
            'title' => array('div.a-spacing-none>div.a-spacing-micro>a>h2.a-size-small', 'text'),
            'price' => array('div.a-spacing-none>a.a-link-normal>span', 'text'),
            'link'  => array('div.a-spacing-none>div.a-spacing-micro>a', 'href'),
        ), 'li>div.s-item-container')->getData(function ($item) {
            $item['link'] = QueryList::Query($item['link'], array(
                'comment' => array('div#averageCustomerReviews_feature_div>div#averageCustomerReviews>span.a-declarative>a#acrCustomerReviewLink>span', 'text'),
                'price'   => array('div#unifiedPrice_feature_div>div#price>span#priceblock_ourprice', 'text'),

            ))->data;
            return $item;
        });

        // $file = fopen('/home/halo/working/query/QueryList/Brooks (44).xls', 'w');
        // fwrite($file, "商品名称\t价格\t评论数\t\n");

        foreach ($hj as $key => $value) {

            $title = str_replace('\r', '', $value['title']);
            $price = str_replace('\r', '', $value['price']);
            $title = str_replace('\n', '', $title);
            $price = str_replace('\n', '', $price);
            $price = str_replace(' ', '', $price);

            foreach ($value['link'] as $v) {
                $comment = trim($v['comment']);
                $comment = str_replace('\r', '', $comment);
                $comment = str_replace('\n', '', $comment);
            }

            if (strlen($price) > 1) {
            $array = explode('$', $price);
            $price = $array[1];
            } else {  $price = 0; }
            if (count($value['link']) < 1) {$comment = "0";}
            if (count($value['link']) > 0) {$comment = strstr($comment, 'c', true);}
            $sale = (float)$price * (float)$comment;
            DB::insert('insert into Playtex (title, price, comment, sale) values (?, ?, ?, ? )',
                [$title, $price, $comment, $sale]);
            // fwrite($file, $title . "\t" . $price . "\t" . $comment . "\t\n");
        }
        // fclose($file);
        echo "成功";
    }
}

<?php

/*
* Auteur: Quentin Fasler
* Description : récupère les  "Social Meta Tags" d'une page web
* Version: 1.0.0

*/
class SocialMetaTags {

    public $ordre = [];
	private $html;

    /**
     * constructeur objet MetaPage
     * 
     * getMeta retournera le premier element disponible
     * 
     * order possible twitter,og,html
     * defaut twitter,og,html
     * no order []
    */
    function __construct($order = ["twitter","og","html"]) {
        $this->ordre = $order;       
    }

    /**
     * Retourne le meta donnes d'une page mais ignore pas les meta sans le champ "name" (contrairement a get_meta_tags)
     * 
     * ressource : https://github.com/tommyku/php-html-meta-parser 
     * @param [type] $url
     * @return void
     */
    private function getMetaTags($url) {
        $html = new DOMDocument;
		libxml_use_internal_errors(true);
        $html->loadHTML(mb_convert_encoding(file_get_contents($url), 'HTML-ENTITIES', 'UTF-8')); 
		$metatags = $html->getElementsByTagName("meta");
		$tmeta = array();
		for ($i=0; $i<$metatags->length; ++$i) {
			$item = $metatags->item($i);
			$name = $item->getAttribute('name');
			
			if (empty($name)) {
				$tmeta[$item->getAttribute('property')] = $item->getAttribute('content');
			}
			else {
				$tmeta[$name] = $item->getAttribute('content');
			}
		}
		return $tmeta;
	}
    /**
     * Recuper le title de la page
     *
     * @param [string] $url
     * @return string title de la page
     */
    private function page_title($url) {
        $fp = file_get_contents($url);
        if (!$fp) 
            return null;

        $res = preg_match("/<title>(.*)<\/title>/siU", $fp, $title_matches);
        if (!$res) 
            return null; 

        // Clean up title: remove EOL's and excessive whitespace.
        $title = preg_replace('/\s+/', ' ', $title_matches[1]);
        $title = trim($title);
        return $title;
    }

    /**
     * Recupere les metas d'une page
     *
     * @param [string] $url
     * @return array string meta de la page (titre,description,image,imagealt,site)
     */
    public function getTags($url){
        if($url!=""){
            $arrMeta = self::getMetaTags($url); 

            $arrMetaFormate = [];
     
            //Twitter
            if(isset($arrMeta["twitter:title"])){
                 $Meta = [];
                 $Meta["titre"] = isset($arrMeta["twitter:title"]) ? $arrMeta["twitter:title"] :null;
                 $Meta["description"] = isset($arrMeta["twitter:description"]) ? $arrMeta["twitter:description"] :null; 
                 $Meta["image"] = isset($arrMeta["twitter:image"]) ? $arrMeta["twitter:image"] :null;     
                 $Meta["imagealt"] = isset($arrMeta["twitter:image:alt"]) ? $arrMeta["twitter:image:alt"] :null;   
                 $Meta["site"] =  isset($arrMeta["twitter:site"]) ? $arrMeta["twitter:site"] :null;  
                 $arrMetaFormate["twitter"]=$Meta;
     
            }

            //Open Graph (og)
            if(isset($arrMeta["og:title"])){
                $Meta = [];
                $Meta["titre"] = isset($arrMeta["og:title"]) ? $arrMeta["og:title"] :null ;
                $Meta["description"] =isset($arrMeta["og:description"]) ? $arrMeta["og:description"] :null ;
                $Meta["image"] =isset($arrMeta["og:image"]) ? $arrMeta["og:image"] :null ;    
                $Meta["imagealt"] =null;
                $Meta["site"] = isset($arrMeta["og:site_name"]) ? $arrMeta["og:site_name"] :null;

                $arrMetaFormate["og"]=$Meta;
    
           }

            //html
            if(isset($arrMeta["description"])){
                $Meta = [];
                $Meta["titre"] = self::page_title($url);
                $Meta["description"] = isset($arrMeta["description"]) ? $arrMeta["description"] :null;
                $Meta["image"] =null;    
                $Meta["imagealt"] =null;
                $Meta["site"]  = null;
                $arrMetaFormate["html"]=$Meta;               
    
           }

           if(!empty($this->ordre)){
               //Filtre
                foreach ( $this->ordre as $unFiltre) {
                    //retourne meta avec filtre
                    if(isset($arrMetaFormate[$unFiltre])){
                        return $arrMetaFormate[$unFiltre];
                    }
                }
                //Aucune meta trouve avec le filtre
                return null;
           }
           else{
               //retourne toutes les metas
               return $arrMetaFormate;
           }
          
        }
        return null;     

    }
    /**
     * Recupere toutes les metas d'une page
     *
     * @param [string] $url
     * @return array string meta de la page
     */
    public function getAll($url){
        return  get_meta_tags($url); 
    }

}

<?php
/**
 * This file is part of MediathekMiner.
 * MediathekMiner is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * Foobar is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details
 *
 * You should have received a copy of the GNU General Public License along with Foobar. If not, see http://www.gnu.org/licenses/.
 *
 *
 * @package     Mediathek
 * @subpackage  NebisImport
 * @author      Juergen Enge (juergen@info-age.net)
 * @copyright   (C) 2016 Academy of Art and Design FHNW
 * @license     http://www.gnu.org/licenses/gpl-3.0
 * @link        http://mediathek.fhnw.ch
 *
 */

/**
 * @namespace
 */

namespace Mediathek;

/**
 * Handling of MARC Data from Nebis
 *
 */

class IKUVidEntity extends SOLRSource {
    private static $videotable = 'source_ikuvid';
    private $json = null;
    private $idprefix = 'ikuvid';
    private $db = null;
    private $barcode = null;
    private $signature = null;
    private $loans = null;
    private $authors = null;
    private $tags = null;
    private $cluster = null;
    private $licenses = null;
    private $urls = null;
    private $signatures = null;
    private $online = false;

    static $done = array(
"323",
"336",
"337",
"338",
"3002",
"3005",
"3006",
"3007",
"3008",
"3010",
"3013",
"3014_b",
"3014",
"3015",
"3016",
"3017",
"3020",
"3021",
"3023",
"3024",
"3025",
"3026",
"3027",
"3028",
"3050",
"3052",
"3053",
"3056",
"3057",
"3058",
"3060",
"3061",
"3062",
"3063",
"3065",
"3066",
"3067",
"3068",
"3069",
"3070",
"3071",
"3072",
"3073",
"3074",
"3075",
"3076",
"3077",
"3078",
"3079",
"3081",
"3082",
"3083",
"3085",
"3088",
"3089",
"3090",
"3091",
"3092",
"3093",
"3095",
"3096",
"3097",
"3098",
"3099",
"3100",
"3101",
"3103",
"3104",
"3106",
"3107",
"3108",
"3109",
"3110",
"3112",
"3114",
"3117",
"3118",
"3119",
"3124",
"3125",
"3127",
"3128",
"3129",
"3131",
"3132",
"3133",
"3134",
"3135",
"3136",
"3137",
"3141",
"3144",
"3145",
"3146",
"3148",
"3150_Dx",
"3150",
"3151",
"3152",
"3153",
"3154",
"3159",
"3160",
"3161",
"3162",
"3163",
"3164",
"3166",
"3167",
"3168",
"3169",
"3170",
"3171",
"3172",
"3173",
"3174",
"3175",
"3176",
"3178",
"3179",
"3181",
"3183",
"3184",
"3185",
"3186",
"3187",
"3188",
"3189",
"3191",
"3192",
"3193",
"3194",
"3195",
"3196",
"3197",
"3198",
"3200",
"3201",
"3202",
"3205",
"3206",
"3207",
"3211",
"3212",
"3213",
"3215",
"3216",
"3217",
"3218",
"3219",
"3222",
"3223",
"3224",
"3225",
"3226",
"3227",
"3228",
"3229",
"3230",
"3231",
"3232",
"3235",
"3236",
"3237",
"3240",
"3241",
"3242",
"3243",
"3245",
"3246",
"3252",
"3253",
"3257",
"3258",
"6002",
"6003",
"6004",
"6005",
"6007",
"6008",
"6009",
"6010",
"6011",
"6012",
"6013",
"6014",
"6018",
"6019",
"6020",
"6024",
"6025",
"6026",
"6027",
"6028",
"6029",
"6036",
"6037",
"6038",
"6039",
"6040",
"6041",
"6042",
"6046",
"6047",
"6048",
"6049",
"6050",
"6053",
"6054",
"6055",
"6056",
"6057",
"6058",
"6064",
"6065",
"6069",
"6072",
"6073",
"6076",
"6077",
"6080",
"6081",
"6083",
"6084",
"6085",
"6086",
"6087",
"6088",
"6089",
"6090",
"6091",
"6092",
"6093",
"6094",
"6095",
"6096",
"6097",
"6098",
"6101",
"6102",
"6103",
"6104",
"6105",
"6106",
"6107",
"6109",
"6110",
"6112",
"6113",
"6114",
"6115",
"6116",
"6117",
"6118",
"6119",
"6120",
"6121",
"6122",
"6123",
"6124",
"6125",
"6126",
"6127",
"6128",
"6129",
"6131",
"6132",
"6133",
"6134",
"6136",
"6138",
"6139",
"6140",
"6141",
"6142",
"6143",
"6144",
"6147",
"6148",
"6150",
"6152",
"6153",
"6154",
"6156",
"6158",
"6159",
"6160",
"6163",
"6164",
"6165",
"6169",
"6170",
"6172",
"6174",
"6175",
"6176",
"6177",
"6178",
"6179",
"6180",
"6181",
"6182",
"6184",
"6185",
"6186",
"6188",
"6189",
"6190",
"6191",
"6192",
"6193",
"6194",
"6195",
"6196",
"6197",
"6199",
"6200",
"6201",
"6202",
"6203",
"6204",
"6205",
"6206",
"6207",
"6208",
"6209",
"6210",
"6211",
"6212",
"6214",
"6215",
"6216",
"6218",
"6219",
"6221",
"6222",
"6223",
"6224",
"6225",
"6227",
"6228",
"6229",
"6231",
"6232",
"6233",
"6234",
"6235",
"6237",
"6238",
"6241",
"6242",
"6243",
"6244",
"6246",
"6247",
"6248",
"6251",
"6253",
"6254",
"6255",
"6256",
"6257",
"6258",
"6259",
"6261",
"6263",
"6264",
"6265",
"6266",
"6267",
"6268",
"6269",
"6270",
"6271",
"6272",
"6273",
"6274",
"6275",
"6276",
"6277",
"6280",
"6281",
"6282",
"6283",
"6284",
"6285",
"6286",
"6287",
"6289",
"6291",
"6292",
"6293",
"6294",
"6295",
"6296",
"6297",
"6298",
"6299",
"6300",
"6301",
"6305",
"6306",
"6307",
"6308",
"6309",
"6310",
"6311",
"6313",
"6314",
"6315",
"6316",
"6317",
"6319",
"6320",
"6321",
"6323",
"6324",
"6325",
"6326",
"6327",
"6329",
"6330",
"6331",
"6332",
"6333",
"6334",
"6335",
"6337",
"6338",
"6339",
"6341",
"6342",
"6343",
"6345",
"6346",
"6347",
"6348",
"6349",
"6350",
"6351",
"6353",
"6355",
"6357",
"6358",
"6359",
"6360",
"6362",
"6364",
"6365",
"6366",
"6367",
"6368",
"6369",
"6370",
"6371",
"6376",
"6378",
"6380",
"6381",
"6382",
"6384",
"6386",
"6388",
"6390",
"6391",
"6392_b",
"6392",
"6393",
"6395",
"6396",
"6398",
"6400",
"6401",
"6402",
"6403",
"6404",
"6405",
"6406",
"6407",
"6408",
"6409",
"6410",
"6411",
"6412",
"6413",
"6414",
"6415",
"6416",
"6417",
"6418",
"6419",
"6420",
"6421",
"6422",
"6423",
"6425",
"6426",
"6427",
"6429",
"6430",
"6431",
"6432",
"6433",
"6434",
"6436",
"6437",
"6438",
"6439",
"6440",
"6442",
"6445",
"6446",
"6447",
"6448",
"6449",
"6450",
"6451",
"6452",
"6453",
"6454",
"6455",
"6456",
"6457",
"6458",
"6459",
"6460",
"6468",
"6470",
"6471",
"6472",
"6473",
"6474",
"6475",
"6476",
"6477",
"6478",
"6479",
"6481",
"6482",
"6483",
"6485",
"6486",
"6487",
"6488",
"6489",
"6490",
"6493",
"6494",
"6496",
"6497",
"6499",
"6500",
"6501",
"6502",
"6504",
"6505",
"6506",
"6507",
"6508",
"6509",
"6510",
"6511",
"6512",
"6513",
"6514",
"6515",
"6516",
"6517",
"6518",
"6519",
"6520",
"6522",
"6525",
"6526",
"6528",
"6529",
"6531",
"6532",
"6533",
"6534",
"6535",
"6537",
"6538",
"6539",
"6540",
"6541",
"6542",
"6543",
"6544",
"6545",
"6546",
"6547",
"6548",
"6550",
"i001",
"i002",
"i003",
"i004",
"i005",
"i006",
"i007",
"i008",
"i009",
"i010",
"i011",
"i012",
"i013",
"i014",
"i015",
"i016",
"i017",
"i018",
"i019",
"i020",
"i021",
"i022",
"i023",
"i024",
"i025",
"i026",
"i027",
"i028",
"i029",
"i030",
"i031",
"i032",
"i033",
"i034_1",
"i034_2",
"i035",
"i036",
"i037",
"i038",
);


    function __construct( \ADOConnection $db ) {
        $this->db = $db;
    }

    public function loadFromDoc( $doc) {
    	$this->data = ( array )json_decode( gzdecode( base64_decode( $doc->metagz )));
    	$this->id = $doc->originalid;
    }

    public function reset() {
    	parent::reset();
        $this->json = null;
        $this->barcode = null;
        $this->signature = null;
        $this->authors = null;
        $this->loans = null;
        $this->tags = null;
        $this->licenses = null;
        $this->urls = null;
        $this->signatures = null;
        $this->online = false;

    }

    function loadFromDatabase( string $id, string $idprefix ) {
        $this->reset();

        $this->id = $id;
        //$this->idprefix = $idprefix;

        $sql = "SELECT * FROM `".self::$videotable."` WHERE `Archiv-Nr` = ".$this->db->qstr( $id );
        $this->data = $this->db->GetRow( $sql );

    }

    public function getID() {
        return $this->idprefix.str_pad($this->id, 9, '0', STR_PAD_LEFT );
    }

	public function getOriginalID() {
		return $this->id;
	}

    public function getSource() {
        return 'IKUVid';
    }

    public function getType() {
		return "MovingImage";
	}


	public function getEmbedded() {
		return array_search($this->id, IKUVidEntity::$done ) !== false;
	}

	public function getOpenAccess() {
		return false;
	}

	public function getLocations() {
		return array( 'E75:Mediathek' );
	}

    public function getTitle() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $title = trim( $this->data['Titel1'] );
        if( strlen( trim( $this->data['Titel2'] )))
            $title .= '. '.trim( $this->data['Titel2'] );

        return $title;
    }

    public function getPublisher() {
        return null;
    }

    public function getYear() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $year = intval( substr( $this->data['Produktionsjahr'], 0, 4 ));
        return $year ? $year : null;
    }

    public function getCity() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        return null;
    }

	public function getTags() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );

        $this->tags = array();
        if( strlen(trim( $this->data['Medium'])))
            $this->tags[] = 'index:medium:ikuvid/'.md5( trim( $this->data['Medium']) ).'/'.trim( $this->data['Medium']);
        if( strlen(trim( $this->data['Techn Daten'])))
            $this->tags[] = 'index:tech:ikuvid/'.md5( trim( $this->data['Techn Daten']) ).'/'.trim( $this->data['Techn Daten']);
        if( strlen(trim( $this->data['Kategorie'])))
            $this->tags[] = 'index:category:ikuvid/'.md5( trim( $this->data['Kategorie']) ).'/'.trim( $this->data['Kategorie']);
        if( strlen(trim( $this->data['Stichwort'])))
            $this->tags[] = 'index:keyword:ikuvid/'.md5( trim( $this->data['Stichwort']) ).'/'.trim( $this->data['Stichwort']);

        $this->tags = array_unique( $this->tags );
        $this->cluster = array();
        foreach( $this->tags as $tag ) {
            if( substr( $tag, 0, strlen( 'index:category' )) == 'index:category'
               || substr( $tag, 0, strlen( 'index:keyword' )) == 'index:keyword' ) {
                $ts = explode( '/', $tag );
                $this->cluster[] = $ts[count( $ts )-1];
            }
        }
        $this->cluster = array_unique( $this->cluster );

        return $this->tags;
	}


    public function getCluster() {
        if( $this->cluster == null) $this->getTags();
        return $this->cluster;
    }

    public function getSignatures() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );

        return array();
    }

    public function getAuthors() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        if( $this->authors == null ) {
            $this->authors = array();
            if( strlen(trim( $this->data['Autor Regie'])))
                 $this->authors[] = trim( $this->data['Autor Regie']);
        }
        return $this->authors;
    }

    public function getLoans() {
        return array();
    }

    public function getBarcode() {
        return null;
    }

    public function getSignature() {
        return null;
    }

    public function getLicenses() {
        if( $this->licenses == null ) {
            $this->licenses = array( 'restricted' );
        }
        return $this->licenses;
    }

    public function getURLs() {
        return array();
    }

    public function getSys() {
        return $this->id;
    }

    public function getMeta() {
        return json_encode( $this->data );
    }

    public function getOnline() {
		return array_search("{$this->id}", IKUVidEntity::$done ) !== false;
    }

   public function getAbstract() {
        if( $this->data == null ) throw new \Exception( "no entity loaded" );
        $bem = trim( $this->data['Bemerkungen']);
        return strlen( $bem ) ? $bem : null;
    }
   public function getContent() { return null; }
   public function getCodes() { return array(); }
   public function getIssues() { return array(); }
   public function getLanguages() {
       $l = array();
       if( $this->data['Originalsprache']) $l[] = $this->data['Originalsprache'];
       if( $this->data['Sprachen 2-Kanal']) $l[] = $this->data['Sprachen 2-Kanal'];
       return $l;
   }

    public function getMetaACL() { return array( 'fhnw.ch_14/user' ); }
    public function getContentACL() { return array( 'fhnw.ch_14/user' ); }
    public function getPreviewACL() { return array( 'fhnw.ch_14/user' ); }
    public function getCatalogs() { return array( $this->getSource() ); }

}

?>

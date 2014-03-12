<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/03/14
 * Time: 18:25
 */

class ApiManagerTest extends PHPUnit_Framework_TestCase
{
    private $accessorProviderMock;
    private $apiProviderMock;
    private $filesProviderMock;
    private $sut;

    public function setUp()
    {
        $_SESSION['url'] =  "594262502f6546615042696335634a49734e63692b4c6e473956316d705a684c4b4974313430514a73716f6f4b656c754d576c536e574e766456706f6b4a31305533795338322b677353644945556248416c61504a4a684b6f434649626f614a71716a704a5671794c437652372f39526d696d594d42625238432b7957563954";
        $_SESSION['token'] = '484a704e7a2f596453366730526e6c666a544346735a516662343646564f79327441526a68463436527a33765162696e316b357567375732485642713078614b6b7766526f2f4f6a497a516a396b74485161526379774c62713747675432734d5a397267644467674350487070497632434e54366d6c596a6c3241344c50525a6e317565696d656570797856526f737853754c414644494d72456c4866315a4871445641775566397233396b6b763930386c7766526e317072644357414a354c4869357a2f44636c67694f495249756854445861446b7071316830497a6a36667569306549424f74704444625a38547878457332423448446b55653236706f7437425072674236767542507476556e79464d6439326733314f694c6f6441424e68436d6341306b714b63306f6154664c4d38676752565a5762686b474c5333676b317355575861526a576443684a4d4c3675534c774b34654b59364a3848756570584c6f4770634474535265764e55507456366931362b6c4451786f35766250584830716e3333377939324a5a5a4c763147666870584e7059687a516f327552476f2f794a432f48766832454843585767486945314736684c794d2f5630746f49706246696a627548475a6a49694a61792f7a764578694d674b57686636703966625a42587376422b395034554d514a4a66773858326d46326144374656356e763633327456483645785742324d7369615430616657385463774b784e5844334b6456345a306e3336415973504f4636583855573766324f435a4969544c4c6c4d663574744d786f5378576b4b2b70436e42306543625056456543472f352b52753943554f4d5369364d493339435766314a544537524c765a49357863614f6c3775326934426448677a4e4b476e582b6d435350766f39784963356d6c4558585730572f6e4a6f616430526265466b685377595371667237355273326a58704a545a39645064654c6d724d415135345566387972586c797579396b37366c642f51526e3052676e6a31667341626e484477784d76493543415677575233676c35656854544d466438463978353138616848626d3254524a754b696535454c4b6a316f785236573651516f3747546c4c506d3450507944624330436e76304f694572625674643461636c38423152504c6b376370622b56342f357275373865547276655974784b666662326b6a746f68644364356e5a626951726e506767444846634965494a45596643675a526a71355a565851344f6178504c6f4e54376b4345437968704d69655835714157646a775351444b327241712b632f344c61465950445572756a73683757765038326b6154387343615a4c315252745276396954466b4a63617559326a586b376f6a45522b6f45644e54753535625156337a33714a6b63716f566374446d74577847627a6e32736842324c2f6167776b672f336237497466566c516162416d39624b4f7a367034566d612f37575970706c39645546524c724b76425536317a7838796e414f4c534f33592f5932554c3769466c706d4e4b3269333275504569692f4c3061313555644972446e444d773956584875702f675177354d304d6b5649657452544a38436d2f2b34386567476967625a6e442f5a4e4c3749542f72576b4d663545754d70345171736b7630522f492b4239725244556d69323579796d70697479323467655158364772504d4561586d2f656c54636e4c6d56703851386278704844784e6d79365573504a5a646671704f753563784545416d523166414b696468366769776a354f51544f34355159785355504a5333352f573358754a3874664536335a496a724d6346756b47447255656c55414d464e4b625431503631526450626b4e49676b34306d35555353494f6d6e5a4d784c7743367351784930594142525954692f5935775864344948556c794f4f397238567568797a7a62596a734e4a6365672f33436575424d795a614e383852576562326c6c39577158674a62344b6c545963757a645375376c516d6d6957435a356c66556a6671514a706273524859496142312f462b786761554e7a496f3350597775564c735972412f4855365262676c344d4a594a6945754a452b466a42416e6e6c4454646770367a6b64765176636555363945725444574c5552785934636166325757414e42324f432f65796d4964672f55752f6e3371626d56594958466d564350393245474f572f3970564b5056377a705250374d5a772b4d4f794c644f344d63514e34346643754f4d41454e6b665a4465514f4c6573424157335572432b514377776579584b72653555484a323542544276307063716a614f4b4e7178564658376e6d3977324864374c517a613832635a377a30786a50584e7a6e5a6774494f53777868647a78556b746545567a37705a6f4e344233575a3843614c44346d724a67764e6d773366464d78736c57724562505a614c616868396b6e30374f675644555055567050625455446f7252656b41313077794761782b654b6f617667624974372f75484a3076777276566b4e6e64416266394859704268426f777649385757766b375632365741347a48366a66684d66546b4a6f654e786a384e3239725a42656a397a49737144574954356f48414b4d75624855756a3346344b665a62362f787933643530704e797942666d776c384c46324d6633637753785a375375724553714e4837534c6b666f794c414558667857343237697967487332656b536c542b2b2f6944542f4c2b31562b6359514e32622f5051347645356e59543942686a4466764338756e614b3130677374315236456963496d55773848444830534b7a534f76594335524b766c734f63687078396347485a706c6d432b6d6a46627344305077523650314b432b2b4838546e4e34634b513833674669335a2f37324d302b785a423943443273376d52596c68584d6373595535523178487265526a336d486c47497161394361476f3473426144417a41754752654268667661412b7561396763484c7a5231517769334b6f666742455766503371715574544b6a4b30455348474e665357394e3735332f754552456d5746644947776830735448472b424e6c6c69587541494b754b2f6b394b465162646376475037712b534545424d7276654336373163735635426e4f4d72336d58736e6f6e4f6a38317734704e3050784e4c506d4c726d3235554e6e44535677305a58327473736f53597062326e53443144346f6142683646536c343341316558372b497268356934775235307855703744326369626c6f465272393676696753413436375163584844414e78393574532b537043427a2f595a7747594d4b797077644d79762f657377356f542b4f386e332b334c70593665765269364c687776645264476161626573763835436f756b516a717267632b367879634430676e5143736e66546c52795330695a51315a6a55485363742f4b4672397838473146396d756b43577457672b686a72663455354b63754252772f35444e4a6f69707456444854527a5432735a3059474568686e4b2b704f6b69722b7a5a5749707a304f5a3938644530647232326732467a77464e697a5762485a657664652f3871483477782b59682b6b6b6e68686d6e6533764b4f4c6f7033443662566a335a65746174356c5576534e764362376d31717756664b754969356b78715969654a71565930525870746c377275504439637637787a412f486e4a7063784875396672724e47455a773046654251346a4d517656466571514733504755747a2b5439524c744a416f5a6471575950795032772b3055644a42594b676a7a7a612b68673d3d';
        $this->accessorProviderMock = $this->getMock('AccessorProvider');
        $this->apiProviderMock = $this->getMock('ApiProvider');
        $this->filesProviderMock = $this->getMock("FilesProvider");
        $this->sut = new ApiManager($this->accessorProviderMock,$this->apiProviderMock,$this->filesProviderMock);
    }

    public function tearDown()
    {
        $_SESSION = array();
    }

    /**
     * method: getProcessDataU1db
     * when: called
     * with: paramTextJson
     * should: returnCorrect
     */
    public function test_getProcessDataU1db_called_paramTextJson_returnCorrect()
    {
        $json = '{"type":"select","lista":[{"file_id":5}]}';
        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessDataU1db')
            ->with($json)
            ->will($this->returnValue('[{"status": "NEW", "mimetype": "inode/directory", "parent_file_id": "null", "checksum": 0, "client_modified": "2013-11-11 15:40:45.784", "filename": "helpFile", "is_root": false, "version": 1, "file_id": 5, "server_modified": "2013-11-11 15:40:45.784", "path": "/", "user": "web", "is_folder": false, "size": 0}, {"status": "NEW", "mimetype": "inode/directory", "parent_file_id": "null", "checksum": 0, "client_modified": "2013-11-11 15:40:45.784", "filename": "helpFile", "is_root": false, "version": 1, "file_id": -7755273878059615652, "server_modified": "2013-11-11 15:40:45.784", "path": "/", "user": "web", "is_folder": false, "size": 0}]'));

        $this->sut->getProcessDataU1db($json);
    }


    /**
     *method: getMetadata
     * when: called
     * with: path
     * should: calledU1dbWithoutData
     */
    public function test_getMetadata_called_path_calledU1dbWithoutData()
    {
        $metadata = '{"file_id":"null","parent_file_id":"null","filename":"root","path":null,"is_folder":true,"status":null,"user":null,"version":null,"checksum":null,"size":null,"mimetype":null,"is_root":true,"contents":[{"file_id":-7755273878059615652,"parent_file_id":"null","filename":"helpFolder","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-11 15:40:45.784","client_modified":"2013-11-11 15:40:45.784","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":-5201053391767961053,"parent_file_id":"null","filename":"New File.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-11-26 16:00:06.308","client_modified":"2013-11-26 16:00:06.307","user":"web","version":20,"checksum":122290589,"size":8,"mimetype":"application/x-empty","chunks":[]},{"file_id":-3378160743781590173,"parent_file_id":"null","filename":"Bienvenido.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-12 13:49:46.262","client_modified":"2013-12-12 13:49:46.261","user":"web","version":6,"checksum":1705643629,"size":50,"mimetype":"application/x-empty","chunks":[]},{"file_id":-2705812544177220237,"parent_file_id":"null","filename":"images","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-10-07 17:58:02.213","client_modified":"2013-07-10 17:42:19.0","user":"ast_cotes201310071757","version":1,"checksum":0,"size":4096,"mimetype":"inode/directory","is_root":false},{"file_id":-1478707423980200270,"parent_file_id":"null","filename":"Cloudspaces_trial","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-12-10 22:53:21.052","client_modified":"2013-12-10 22:53:21.052","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":819819698545290447,"parent_file_id":"null","filename":"Documents","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-18 13:48:51.269","client_modified":"2013-11-18 13:48:51.269","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":1977451714816609267,"parent_file_id":"null","filename":"test.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-03 12:04:49.392","client_modified":"2013-12-03 12:04:49.391","user":"web","version":5,"checksum":94306754,"size":5,"mimetype":"text/plain","chunks":[]},{"file_id":3894030578176289733,"parent_file_id":"null","filename":"hola","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-18 12:11:35.656","client_modified":"2013-11-18 12:11:35.656","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":6377614534029818696,"parent_file_id":"null","filename":"testtt","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-15 13:00:00.073","client_modified":"2013-11-15 13:00:00.073","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false}]}';
        $this->exerciseGetMetadataWithoutData("home:///stacksync",$metadata);
    }

    /**
     *method: getMetadata
     * when: called
     * with: pathAndFileId
     * should: calledU1dbWithoutData
     */
    public function test_getMetadata_called_pathAndFileId_calledU1dbWithoutData()
    {
        $metadata = '{"file_id":-1478707423980200270,"parent_file_id":"null","filename":"Cloudspaces_trial","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-12-10 22:53:21.052","client_modified":"2013-12-10 22:53:21.052","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false,"contents":[{"file_id":2681230491652302322,"parent_file_id":-1478707423980200270,"filename":"Cloudspaces demo text.txt","path":"/Cloudspaces_trial/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-10 22:54:59.665","client_modified":"2013-12-10 22:54:59.664","user":"web","version":2,"checksum":3674040746,"size":299,"mimetype":"text/plain","chunks":[]},{"file_id":-2096699531480976652,"parent_file_id":-1478707423980200270,"filename":"Authentication.jpg","path":"/Cloudspaces_trial/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-10 22:55:56.393","client_modified":"2013-12-10 22:55:56.392","user":"web","version":2,"checksum":2876523746,"size":574156,"mimetype":"image/jpeg","chunks":[]}]}';
        $this->exerciseGetMetadataWithoutData("home:///stacksync/Cloudspaces_trial",$metadata,-1478707423980200270);
    }


    /**
     *method: getMetadata
     * when: called
     * with: path
     * should: calledU1dbSameData
     */
    public function test_getMetadata_called_path_calledU1dbSameData()
    {
        $metadata = '{"file_id":"null","parent_file_id":"null","filename":"root","path":null,"is_folder":true,"status":null,"user":null,"version":null,"checksum":null,"size":null,"mimetype":null,"is_root":true,"contents":[{"file_id":-7755273878059615652,"parent_file_id":"null","filename":"helpFolder","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-11 15:40:45.784","client_modified":"2013-11-11 15:40:45.784","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":-5201053391767961053,"parent_file_id":"null","filename":"New File.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-11-26 16:00:06.308","client_modified":"2013-11-26 16:00:06.307","user":"web","version":20,"checksum":122290589,"size":8,"mimetype":"application/x-empty","chunks":[]},{"file_id":-3378160743781590173,"parent_file_id":"null","filename":"Bienvenido.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-12 13:49:46.262","client_modified":"2013-12-12 13:49:46.261","user":"web","version":6,"checksum":1705643629,"size":50,"mimetype":"application/x-empty","chunks":[]},{"file_id":-2705812544177220237,"parent_file_id":"null","filename":"images","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-10-07 17:58:02.213","client_modified":"2013-07-10 17:42:19.0","user":"ast_cotes201310071757","version":1,"checksum":0,"size":4096,"mimetype":"inode/directory","is_root":false},{"file_id":-1478707423980200270,"parent_file_id":"null","filename":"Cloudspaces_trial","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-12-10 22:53:21.052","client_modified":"2013-12-10 22:53:21.052","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":819819698545290447,"parent_file_id":"null","filename":"Documents","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-18 13:48:51.269","client_modified":"2013-11-18 13:48:51.269","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":1977451714816609267,"parent_file_id":"null","filename":"test.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-03 12:04:49.392","client_modified":"2013-12-03 12:04:49.391","user":"web","version":5,"checksum":94306754,"size":5,"mimetype":"text/plain","chunks":[]},{"file_id":3894030578176289733,"parent_file_id":"null","filename":"hola","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-18 12:11:35.656","client_modified":"2013-11-18 12:11:35.656","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":6377614534029818696,"parent_file_id":"null","filename":"testtt","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-15 13:00:00.073","client_modified":"2013-11-15 13:00:00.073","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false}]}';
        $u1db = '[{"file_id":"null","parent_file_id":"null","filename":"root","path":null,"is_folder":true,"status":null,"user":null,"version":null,"checksum":null,"size":null,"mimetype":null,"is_root":true},{"file_id":-7755273878059615652,"parent_file_id":"null","filename":"helpFolder","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-11 15:40:45.784","client_modified":"2013-11-11 15:40:45.784","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":-5201053391767961053,"parent_file_id":"null","filename":"New File.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-11-26 16:00:06.308","client_modified":"2013-11-26 16:00:06.307","user":"web","version":20,"checksum":122290589,"size":8,"mimetype":"application/x-empty","chunks":[]},{"file_id":-3378160743781590173,"parent_file_id":"null","filename":"Bienvenido.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-12 13:49:46.262","client_modified":"2013-12-12 13:49:46.261","user":"web","version":6,"checksum":1705643629,"size":50,"mimetype":"application/x-empty","chunks":[]},{"file_id":-2705812544177220237,"parent_file_id":"null","filename":"images","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-10-07 17:58:02.213","client_modified":"2013-07-10 17:42:19.0","user":"ast_cotes201310071757","version":1,"checksum":0,"size":4096,"mimetype":"inode/directory","is_root":false},{"file_id":-1478707423980200270,"parent_file_id":"null","filename":"Cloudspaces_trial","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-12-10 22:53:21.052","client_modified":"2013-12-10 22:53:21.052","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":819819698545290447,"parent_file_id":"null","filename":"Documents","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-18 13:48:51.269","client_modified":"2013-11-18 13:48:51.269","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":1977451714816609267,"parent_file_id":"null","filename":"test.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-03 12:04:49.392","client_modified":"2013-12-03 12:04:49.391","user":"web","version":5,"checksum":94306754,"size":5,"mimetype":"text/plain","chunks":[]},{"file_id":3894030578176289733,"parent_file_id":"null","filename":"hola","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-18 12:11:35.656","client_modified":"2013-11-18 12:11:35.656","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":6377614534029818696,"parent_file_id":"null","filename":"testtt","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-15 13:00:00.073","client_modified":"2013-11-15 13:00:00.073","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false}]';

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->will($this->returnValue($u1db));

        $this->filesProviderMock->expects($this->never())
            ->method('createFile')
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->never())
            ->method('deleteFile')
            ->will($this->returnValue(true));


        $this->sut->getMetadata("home:///stacksync");

    }

    /**
     * method: getMetadata
     * when: called
     * with: path
     * should: calledU1dbDistinctData
     */
    public function test_getMetadata_called_path_calledU1dbDistinctData()
    {
        $metadata = '{"file_id":"null","parent_file_id":"null","filename":"root","path":null,"is_folder":true,"status":null,"user":null,"version":null,"checksum":null,"size":null,"mimetype":null,"is_root":true,"contents":[{"file_id":-7755273878059615652,"parent_file_id":"null","filename":"helpFolder","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-11 15:40:45.784","client_modified":"2013-11-11 15:40:45.784","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":-5201053391767961053,"parent_file_id":"null","filename":"New File.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-11-26 16:00:06.308","client_modified":"2013-11-26 16:00:06.307","user":"web","version":20,"checksum":122290589,"size":8,"mimetype":"application/x-empty","chunks":[]},{"file_id":-3378160743781590173,"parent_file_id":"null","filename":"Bienvenido.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-12 13:49:46.262","client_modified":"2013-12-12 13:49:46.261","user":"web","version":6,"checksum":1705643629,"size":50,"mimetype":"application/x-empty","chunks":[]},{"file_id":-2705812544177220237,"parent_file_id":"null","filename":"images","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-10-07 17:58:02.213","client_modified":"2013-07-10 17:42:19.0","user":"ast_cotes201310071757","version":1,"checksum":0,"size":4096,"mimetype":"inode/directory","is_root":false},{"file_id":-1478707423980200270,"parent_file_id":"null","filename":"Cloudspaces_trial","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-12-10 22:53:21.052","client_modified":"2013-12-10 22:53:21.052","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":819819698545290447,"parent_file_id":"null","filename":"Documents","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-18 13:48:51.269","client_modified":"2013-11-18 13:48:51.269","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":1977451714816609267,"parent_file_id":"null","filename":"test.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-03 12:04:49.392","client_modified":"2013-12-03 12:04:49.391","user":"web","version":5,"checksum":94306754,"size":5,"mimetype":"text/plain","chunks":[]},{"file_id":3894030578176289733,"parent_file_id":"null","filename":"hola","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-18 12:11:35.656","client_modified":"2013-11-18 12:11:35.656","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":6377614534029818696,"parent_file_id":"null","filename":"testtt","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-15 13:00:00.073","client_modified":"2013-11-15 13:00:00.073","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false}]}';
        $u1db = '[{"file_id":"null","parent_file_id":"null","filename":"root","path":null,"is_folder":true,"status":null,"user":null,"version":null,"checksum":null,"size":null,"mimetype":null,"is_root":true},{"file_id":1234,"parent_file_id":"null","filename":"helpFolder","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-11 15:40:45.784","client_modified":"2013-11-11 15:40:45.784","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":-5201053391767961053,"parent_file_id":"null","filename":"New File.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-11-26 16:00:06.308","client_modified":"2013-11-26 16:00:06.307","user":"web","version":20,"checksum":122290589,"size":8,"mimetype":"application/x-empty","chunks":[]},{"file_id":-3378160743781590173,"parent_file_id":"null","filename":"Bienvenido.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-12 13:49:46.262","client_modified":"2013-12-12 13:49:46.261","user":"web","version":6,"checksum":1705643629,"size":50,"mimetype":"application/x-empty","chunks":[]},{"file_id":-2705812544177220237,"parent_file_id":"null","filename":"images","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-10-07 17:58:02.213","client_modified":"2013-07-10 17:42:19.0","user":"ast_cotes201310071757","version":1,"checksum":0,"size":4096,"mimetype":"inode/directory","is_root":false},{"file_id":-1478707423980200270,"parent_file_id":"null","filename":"Cloudspaces_trial","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-12-10 22:53:21.052","client_modified":"2013-12-10 22:53:21.052","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":819819698545290447,"parent_file_id":"null","filename":"Documents","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-18 13:48:51.269","client_modified":"2013-11-18 13:48:51.269","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":1977451714816609267,"parent_file_id":"null","filename":"test.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-03 12:04:49.392","client_modified":"2013-12-03 12:04:49.391","user":"web","version":5,"checksum":94306754,"size":5,"mimetype":"text/plain","chunks":[]},{"file_id":3894030578176289733,"parent_file_id":"null","filename":"hola","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-18 12:11:35.656","client_modified":"2013-11-18 12:11:35.656","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":6377614534029818696,"parent_file_id":"null","filename":"testtt","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-15 13:00:00.073","client_modified":"2013-11-15 13:00:00.073","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false}]';

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->will($this->returnValue($u1db));

        $this->filesProviderMock->expects($this->exactly(1))
            ->method('createFile')
            ->will($this->returnValue(true));

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->will($this->returnValue('true'));

        $this->filesProviderMock->expects($this->exactly(1))
            ->method('deleteFile')
            ->will($this->returnValue(true));

        $this->accessorProviderMock->expects($this->at(2))
            ->method('getProcessDataU1db')
            ->will($this->returnValue('true'));


        $this->sut->getMetadata("home:///stacksync");

    }

    /**
     *method: getMetadata
     * when: called
     * with: path
     * should: calledU1dbUpdate
     */
    public function test_getMetadata_called_path_calledU1dbUpdate()
    {
        $metadata = '{"file_id":"null","parent_file_id":"null","filename":"root","path":null,"is_folder":true,"status":null,"user":null,"version":null,"checksum":null,"size":null,"mimetype":null,"is_root":true,"contents":[{"file_id":-7755273878059615652,"parent_file_id":"null","filename":"helpFolder","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-11 15:40:45.784","client_modified":"2013-11-11 15:40:45.784","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":-5201053391767961053,"parent_file_id":"null","filename":"New File.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-11-26 16:00:06.308","client_modified":"2013-11-26 16:00:06.307","user":"web","version":20,"checksum":122290589,"size":8,"mimetype":"application/x-empty","chunks":[]},{"file_id":-3378160743781590173,"parent_file_id":"null","filename":"Bienvenido.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-12 13:49:46.262","client_modified":"2013-12-12 13:49:46.261","user":"web","version":6,"checksum":1705643629,"size":50,"mimetype":"application/x-empty","chunks":[]},{"file_id":-2705812544177220237,"parent_file_id":"null","filename":"images","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-10-07 17:58:02.213","client_modified":"2013-07-10 17:42:19.0","user":"ast_cotes201310071757","version":1,"checksum":0,"size":4096,"mimetype":"inode/directory","is_root":false},{"file_id":-1478707423980200270,"parent_file_id":"null","filename":"Cloudspaces_trial","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-12-10 22:53:21.052","client_modified":"2013-12-10 22:53:21.052","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":819819698545290447,"parent_file_id":"null","filename":"Documents","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-18 13:48:51.269","client_modified":"2013-11-18 13:48:51.269","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":1977451714816609267,"parent_file_id":"null","filename":"test.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-03 12:04:49.392","client_modified":"2013-12-03 12:04:49.391","user":"web","version":5,"checksum":94306754,"size":5,"mimetype":"text/plain","chunks":[]},{"file_id":3894030578176289733,"parent_file_id":"null","filename":"hola","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-18 12:11:35.656","client_modified":"2013-11-18 12:11:35.656","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":6377614534029818696,"parent_file_id":"null","filename":"testtt","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-15 13:00:00.073","client_modified":"2013-11-15 13:00:00.073","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false}]}';
        $u1db = '[{"file_id":"null","parent_file_id":"null","filename":"root","path":null,"is_folder":true,"status":null,"user":null,"version":null,"checksum":null,"size":null,"mimetype":null,"is_root":true},{"file_id":-7755273878059615652,"parent_file_id":"null","filename":"helpDirectorio","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-11 15:40:45.784","client_modified":"2013-11-11 15:40:45.784","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":-5201053391767961053,"parent_file_id":"null","filename":"New File.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-11-26 16:00:06.308","client_modified":"2013-11-26 16:00:06.307","user":"web","version":20,"checksum":122290589,"size":8,"mimetype":"application/x-empty","chunks":[]},{"file_id":-3378160743781590173,"parent_file_id":"null","filename":"Bienvenido.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-12 13:49:46.262","client_modified":"2013-12-12 13:49:46.261","user":"web","version":6,"checksum":1705643629,"size":50,"mimetype":"application/x-empty","chunks":[]},{"file_id":-2705812544177220237,"parent_file_id":"null","filename":"images","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-10-07 17:58:02.213","client_modified":"2013-07-10 17:42:19.0","user":"ast_cotes201310071757","version":1,"checksum":0,"size":4096,"mimetype":"inode/directory","is_root":false},{"file_id":-1478707423980200270,"parent_file_id":"null","filename":"Cloudspaces_trial","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-12-10 22:53:21.052","client_modified":"2013-12-10 22:53:21.052","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":819819698545290447,"parent_file_id":"null","filename":"Documents","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-18 13:48:51.269","client_modified":"2013-11-18 13:48:51.269","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":1977451714816609267,"parent_file_id":"null","filename":"test.txt","path":"/","is_folder":false,"status":"CHANGED","server_modified":"2013-12-03 12:04:49.392","client_modified":"2013-12-03 12:04:49.391","user":"web","version":5,"checksum":94306754,"size":5,"mimetype":"text/plain","chunks":[]},{"file_id":3894030578176289733,"parent_file_id":"null","filename":"hola","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-18 12:11:35.656","client_modified":"2013-11-18 12:11:35.656","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false},{"file_id":6377614534029818696,"parent_file_id":"null","filename":"testtt","path":"/","is_folder":true,"status":"NEW","server_modified":"2013-11-15 13:00:00.073","client_modified":"2013-11-15 13:00:00.073","user":"web","version":1,"checksum":0,"size":0,"mimetype":"inode/directory","is_root":false}]';

        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->will($this->returnValue($u1db));

        $this->filesProviderMock->expects($this->never())
            ->method('createFile')
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->never())
            ->method('deleteFile')
            ->will($this->returnValue(true));

        $this->filesProviderMock->expects($this->once())
            ->method('renameFile')
            ->will($this->returnValue(true));


        $this->sut->getMetadata("home:///stacksync");

    }

    /**
     *method: createFile
     * when: called
     * with:filenameAndFileAndFilesizeAndPathParentAndFolderParent
     * should: calledU1dbUpdate
     */
    public function test_createFile_called_filenameAndFileAndFilesizeAndFolderParent_calledU1dbUpdate()
    {
        $pathParent = "/Documents/prueba/";
        $folderParent = "hola";
        $metadataProvider = '{"status": "NEW", "mimetype": "application/x-empty", "parent_file_version": null, "parent_file_id": "null", "root_id": "stacksync", "server_modified": "Fri Mar 07 11:55:32 CET 2014", "checksum": 694355124, "client_modified": "Fri Mar 07 11:55:32 CET 2014", "filename": "pruebas.txt", "version": 7, "file_id": -7705621709365758847, "is_folder": false, "chunks": ["A6960EF3C0B501B4C338DE32A6C8E9A5004FE350"], "path": "/hola", "size": 15, "user": "web"}';
        $metadataU1db = '[{"status": "NEW", "mimetype": "inode/directory", "parent_file_id": "null", "checksum": 0, "client_modified": "2013-12-10 22:53:21.052", "filename": "Cloudspaces_trial", "is_root": false, "version": 1, "file_id": "-1478707423980200270", "server_modified": "2013-12-10 22:53:21.052", "path": "/", "user": "web", "is_folder": true, "size": 0}]';
        $this->exerciseCreateFile($metadataU1db,$metadataProvider,$pathParent,$folderParent);
    }

    /**
     *method: createFile
     * when: called
     * with:filenameAndFileAndFilesizeAndPathParent
     * should: calledU1dbUpdate
     */
    public function test_createFile_called_filenameAndFileAndFilesize_calledU1dbUpdate()
    {
        $metadataProvider = '{"status": "NEW", "mimetype": "application/x-empty", "parent_file_version": null, "parent_file_id": "null", "root_id": "stacksync", "server_modified": "Fri Mar 07 11:55:32 CET 2014", "checksum": 694355124, "client_modified": "Fri Mar 07 11:55:32 CET 2014", "filename": "pruebas.txt", "version": 7, "file_id": -7705621709365758847, "is_folder": false, "chunks": ["A6960EF3C0B501B4C338DE32A6C8E9A5004FE350"], "path": "/hola", "size": 15, "user": "web"}';
        $metadataU1db = '';
        $pathParent = '/';
        $this->exerciseCreateFile($metadataU1db,$metadataProvider,$pathParent);
    }

    /**
     *method: createFolder
     * when: called
     * with:folderNameAndIdParent
     * should: calledU1dbUpdate
     */
    public function test_createFolder_called_folderNameAndIdParent_calledU1dbUpdate()
    {
        $metadataProvider = '{"status": "NEW", "mimetype": "inode/directory", "parent_file_version": "", "parent_file_id": "-1478707423980200270", "root_id": "stacksync", "server_modified": "Fri Mar 07 17:22:51 CET 2014", "checksum": 0, "client_modified": "Fri Mar 07 17:22:51 CET 2014", "filename": "TestFolder", "version": 1, "file_id": -3243347967282172526, "is_folder": true, "path": "/Documents/prueba/hola/", "size": 0, "user": "web"}';
        $idParent = "12345";
        $this->exerciseCreateFolder($metadataProvider,$idParent);
    }

    /**
     *method: createFolder
     * when: called
     * with:folderName
     * should: calledU1dbUpdate
     */
    public function test_createFolder_called_folderName_calledU1dbUpdate()
    {
        $metadataProvider = '{"status": "NEW", "mimetype": "inode/directory", "parent_file_version": "", "parent_file_id": "null", "root_id": "stacksync", "server_modified": "Fri Mar 07 17:22:51 CET 2014", "checksum": 0, "client_modified": "Fri Mar 07 17:22:51 CET 2014", "filename": "TestFolder", "version": 1, "file_id": -3243347967282172526, "is_folder": true, "path": "/", "size": 0, "user": "web"}';
        $this->exerciseCreateFolder($metadataProvider);
    }

    /**
     *method: deleteComponent
     * when: called
     * with: idFile
     * should: calledU1dbDelete
     */
    public function test_deleteComponent_called_idFile_calledU1dbDelete()
    {
        $fileId = "546556566";
        $this->exerciseDeleteComponent($fileId);
    }

    /**
     *method: deleteComponent
     * when: called
     * with: idFolder
     * should: calledU1dbDelete
     */
    public function test_deleteComponent_called_idFolder_calledU1dbDelete()
    {
        $folderId = "546556566";
        $this->exerciseDeleteComponent($folderId);
    }

    /**
     *method: renameFile
     * when: called
     * with: idFileAndFilenameAndFileAndFilesize
     * should: calledU1dbDeleteAndInsert
     */
    public function test_renameFile_called_idFileAndFilename_calledU1dbDeleteAndInsert()
    {
        $idFile = "5454455445";
        $metadataProvider = '{"status": "NEW", "mimetype": "application/x-empty", "parent_file_version": null, "parent_file_id": "null", "root_id": "stacksync", "server_modified": "Fri Mar 07 11:55:32 CET 2014", "checksum": 694355124, "client_modified": "Fri Mar 07 11:55:32 CET 2014", "filename": "pruebas.txt", "version": 7, "file_id": -7705621709365758847, "is_folder": false, "chunks": ["A6960EF3C0B501B4C338DE32A6C8E9A5004FE350"], "path": "/hola", "size": 15, "user": "web"}';

        $path = "resources/pruebas.txt";
        $file = fopen($path, "r");
        $fileName = "pruebas.txt";


        $this->apiProviderMock->expects($this->at(0))
            ->method('deleteComponent')
            ->will($this->returnValue(true));


        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->will($this->returnValue('true'));

        $this->apiProviderMock->expects($this->at(1))
            ->method('createFile')
            ->will($this->returnValue($metadataProvider));

        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->will($this->returnValue('true'));

        $this->sut->renameFile($idFile,$fileName,$file,filesize($path));

        fclose($file);
    }

    /**
     *method: donwloadFile
     * when: called
     * with: idFile
     * should: calledDownloadFile
     */
    public function test_downloadFile_called_idFile_calledDownloadFile()
    {
        $idFile = "5454455445";
        $content = "Es una prueba";

        $this->apiProviderMock->expects($this->at(0))
            ->method('downloadFile')
            ->will($this->returnValue($content));

        $this->sut->downloadFile($idFile);
    }

    /**
     *method: renameFolder
     * when: called
     * with: idFolderAndFoldername
     * should: calledU1dbDeleteAndInsert
     */
    public function test_renameFolder_called_idFolderAndFoldername_calledU1dbDeleteAndInsert()
    {
        $idFolder = "12345";
        $folderName = "folderTest";
        $metadataProvider = '{"status": "NEW", "mimetype": "application/x-empty", "parent_file_version": null, "parent_file_id": "null", "root_id": "stacksync", "server_modified": "Fri Mar 07 11:55:32 CET 2014", "checksum": 694355124, "client_modified": "Fri Mar 07 11:55:32 CET 2014", "filename": "folderTest", "version": 1, "file_id":"44444", "is_folder": true, "chunks": ["A6960EF3C0B501B4C338DE32A6C8E9A5004FE350"], "path": "/", "size": 15, "user": "web"}';

        $this->apiProviderMock->expects($this->at(0))
            ->method('deleteComponent')
            ->will($this->returnValue(true));
        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->will($this->returnValue('true'));
        $this->apiProviderMock->expects($this->at(1))
            ->method('createFolder')
            ->will($this->returnValue(json_decode($metadataProvider)));
        $this->accessorProviderMock->expects($this->at(1))
            ->method('getProcessDataU1db')
            ->will($this->returnValue('true'));

        $this->sut->renameFolder($idFolder,$folderName);
    }

    private function exerciseGetMetadataWithoutData($path,$metadata, $fileId = NULL)
    {
        $this->apiProviderMock->expects($this->at(0))
            ->method('getMetadata')
            ->will($this->returnValue(json_decode($metadata)));

        $this->accessorProviderMock->expects($this->at(0))
            ->method('getProcessDataU1db')
            ->will($this->returnValue('[]'));

        $this->filesProviderMock->expects($this->any())
            ->method('createFile')
            ->will($this->returnValue(true));

        $this->accessorProviderMock->expects($this->any())
            ->method('getProcessDataU1db')
            ->will($this->returnValue('true'));

        $this->sut->getMetadata($path,$fileId);
    }

    private function exerciseCreateFile($metadataU1db,$metadataProvider,$pathParent,$folderParent=NULL)
    {
        $path = "resources/pruebas.txt";
        $file = fopen($path, "r");
        $filename = "pruebas.txt";

        $this->apiProviderMock->expects($this->once())
            ->method('createFile')
            ->will($this->returnValue(json_decode($metadataProvider)));

        $sequence = 0;
        if (strlen($metadataU1db) > 0) {
            $this->accessorProviderMock->expects($this->at($sequence))
                ->method('getProcessDataU1db')
                ->will($this->returnValue($metadataU1db));
            $sequence++;
        }

        $this->accessorProviderMock->expects($this->at($sequence))
            ->method('getProcessDataU1db')
            ->will($this->returnValue('[]'));
        $sequence++;

        $this->accessorProviderMock->expects($this->at($sequence))
            ->method('getProcessDataU1db')
            ->will($this->returnValue('true'));
        $this->sut->createFile($filename,$file,filesize($path),$pathParent,$folderParent);
        fclose($file);
    }

    private function exerciseCreateFolder($metadataProvider,$idParent = NULL)
    {
        $foldername = "TestPrueba";
        $this->apiProviderMock->expects($this->once())
            ->method('createFolder')
            ->will($this->returnValue(json_decode($metadataProvider)));

        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessDataU1db')
            ->will($this->returnValue('true'));
        $this->sut->createFolder($foldername,$idParent);
    }

    private function exerciseDeleteComponent($idComponent)
    {
        $this->apiProviderMock->expects($this->once())
            ->method('deleteComponent')
            ->will($this->returnValue(true));

        $this->accessorProviderMock->expects($this->once())
            ->method('getProcessDataU1db')
            ->will($this->returnValue('true'));

        $this->sut->deleteComponent($idComponent);
    }

}

?>
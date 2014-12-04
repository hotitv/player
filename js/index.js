//AngularJS App Object
var app = angular.module("app", []);


/**
 * AppCtrl
 */
app.controller("AppCtrl", function ($scope) {

  console.log("init AppCtrl");

  // binding values
  $scope.mediaid = "";
  $scope.status = "";

  // enable fast click on mobile
  $scope.hammer = new Hammer(document.body);

  // init SoundCloud SDK2
  SC.initialize({client_id: "YOUR CLIENT ID"});



  /**
   * loadMedia
   */
  $scope.loadMedia = function() {
    $scope.onClickBtnStop();
    $scope.status = "loading media...";
    SC.stream("https://api.soundcloud.com/tracks/" + $scope.mediaid, {autoPlay: true}, $scope.onCompleteLoadPlayer);
    // autoPlay is true, but no auto play (´・ω・`)
  };


  /**
   * onCompleteLoadPlayer
   */
  $scope.onCompleteLoadPlayer = function(audiomanager) {
    console.log("onCompleteLoadPlayer()");
    $scope.status = "media loaded.";
    $scope.player = audiomanager._player;
    $scope.player.on("stateChange", $scope.onPlayerStateChange);
    $scope.$evalAsync();
    $scope.player.play();

    /*

    This play() call does not work on mobile! Because of this reason.
    "Audio files can only be loaded from a user-triggered touch (click) event."

    Overcoming iOS HTML5 audio limitations
    http://www.ibm.com/developerworks/library/wa-ioshtml5/

    Making HTML5 audio actually work on mobile | Pupunzi
    http://pupunzi.open-lab.com/2013/03/13/making-html5-audio-actually-work-on-mobile/

    */

    // And also this play() call deos not work!
    setTimeout(function() {
      console.log("try to play after 2sec.");
      $scope.player.play();
    }, 2000);

  };


  /*
   * onPlayerStateChange
   */
  $scope.onPlayerStateChange = function(evt) {
    console.log("onPlayerStateChange():" + evt);
    $scope.status = evt;
    $scope.$evalAsync();
  };


  /*
   * onClickBtnLoad1
   */
  $scope.onClickBtnLoad1 = function(evt) {
    console.log("onClickBtnLoad1()");
    $scope.mediaid = "116811535"; // BabyMetal - Megitsune
    //$scope.mediaid = "119636394";
    $scope.loadMedia();
  };


  /*
   * onClickBtnLoad2
   */
  $scope.onClickBtnLoad2 = function(evt) {
    console.log("onClickBtnLoad2()");
    $scope.mediaid = "116810159"; // BabyMetal - Headbangeeeeerrrrr !!!!!
    //$scope.mediaid = "20103435"; // Ferraz - 1977 （playlist） https://soundcloud.com/hotitv/sets/ferraz-1977
    $scope.loadMedia();
  };


  /*
   * onClickBtnPlay
   */
  $scope.onClickBtnPlay = function(evt) {
    console.log("onClickBtnPlay()");
    $scope.player.play();
  };


  /*
   * onClickBtnStop
   */
  $scope.onClickBtnStop = function(evt) {
    if ($scope.player) {
      console.log("onClickBtnStop()");
      $scope.player.kill();
      $scope.player = null;
    }
  };


});

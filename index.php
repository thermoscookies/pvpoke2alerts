<?php
$defaultchar = ".";
$defaulthelp = "Select your options above and to the right then press the play button above. You alert commands will be shown below for you to copy and paste into a Poracle bot.";

$opts = [
        'http' => [
                'method' => 'GET',
                'header' => [
                        'User-Agent: PHP'
                ]
        ]
];
$context = stream_context_create($opts);
// $tree = file_get_contents('https://api.github.com/repos/pvpoke/pvpoke/git/trees/master?recursive=1', false, $context);
$tree = file_get_contents('tree.json', false, $context);
$treejson = json_decode($tree, true);
$cups = array();
$leagues = array();
$selectcups = '';
$jsleaguecuplist = "var cuplist = [];\r\n";
$debug='';

foreach ($treejson['tree'] as $i) {
    $path = $i['path'];
    // if (preg_match('/^src\/data\/rankings$/', $path)) { //this matches src/data/rankings
    // if (preg_match('/^src\/data\/rankings\/[^\/]+$/', $path)) {  //this matches src/data/rankings/adl
    //     $cupname = substr($path, 18);
    //     $selectcups.='<input type="checkbox" name="' . $cupname . '">' . $cupname . '<BR>';
    // }


    if (preg_match('/^src\/data\/rankings\/.+overall\/[^\/]+json$/', $path)) { //this matches src/data/rankings/all/overall/rankings-10000.json , any numbers
        $cupname = substr($path, 18);
        preg_match('/src\/data\/rankings\/(.+)\/.+\/?\//', $path, $m);
        $cupname = $m[1];
        if (!array_key_exists($cupname, $cups)) {
            $cups[$cupname]=array();
        }
        if (preg_match('/src\/data\/rankings.+rankings-(.+).json?/', $path, $m)) {
            $league = $m[1];
            if (!array_key_exists($league, $leagues)) {
                $leagues[$league] = array();
            }
            $leagues[$league][$cupname] = [];
            $url = 'https://raw.githubusercontent.com/pvpoke/pvpoke/master/src/data/rankings/' . $cupname . '/overall/rankings-' . $league . '.json';
            $cups[$cupname][$league] = 'https://raw.githubusercontent.com/pvpoke/pvpoke/master/src/data/rankings/' . $cupname . '/overall/rankings-' . $league . '.json';

            $jsleaguecuplist.='cuplist["' . $cupname . $league . '"] = "' . $url . '";' . "\r\n";
        }
    }
}

foreach ($cups as $key => $value) {
    ksort($cups[$key]);
    $classes='';
    foreach ($cups[$key] as $key2 => $value2) {
            $classes.=$key2 . ' ';
    }
    $classes.="checkbox";
    $checked="";
    if ($key == "all") { $checked=" checked"; }
    $selectcups.='<label><input type="checkbox" class="' . $classes . '" name="' . $key . '"' . $checked . '>' . $key . '</label><BR>';
}
$selectleagues = '<select name="league" id="league" onchange="myFunction(this.value);">';
ksort($leagues);
foreach ($leagues as $key => $values) {
    // $selected = ($league=='500')? ' selected' : '';
    $classes='';
    foreach ($values as $key2 => $values2) {
        // $debug.=$key2 . ' ';
        $classes.=$key2 . " ";
    }
    $cupname=$key;
    if ($key == "500") {$cupname = "Little Cup";}
    if ($key == "1500") {$cupname = "Great League";}
    if ($key == "2500") {$cupname = "Ultra League";}
    if ($key == "10000") {$cupname = "Master League";}
    $selectleagues .= '<option value="' . $key . '">' . $cupname . "</option>";
}
$selectleagues .="</select>";

?>
<HTML>
    <HEAD>
        <TITLE>pvpoke2alerts</TITLE>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script><?php echo $jsleaguecuplist; ?></script>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> 
        <STYLE>
            table {
                border-collapse:separate;
                border:solid black 2px;
                border-radius:10px;
            }
            /* Chrome, Safari, Edge, Opera */
            input::-webkit-outer-spin-button,
            input::-webkit-inner-spin-button {
              -webkit-appearance: none;
              margin: 0;
            }
            
            /* Firefox */
            input[type=number] {
              -moz-appearance: textfield;
            }
            /*.icon-link-mail { position:absolute; z-index: 2; right:0;}*/
            .results {
                position: relative;
            }
              
            .results i{
                position: absolute;
                right: 5px;
                top: 5px;
                color: orange;
            }
            
            .results .tooltiptext {
              visibility: hidden;
              width: 140px;
              background-color: #555;
              color: #fff;
              text-align: center;
              border-radius: 6px;
              padding: 5px;
              position: absolute;
              z-index: 1;
              bottom: 150%;
              left: 50%;
              margin-left: -105px;
              opacity: 0;
              transition: opacity 0.3s;
            }
            
            .results .tooltiptext::after {
              content: "";
              position: absolute;
              top: 100%;
              left: 50%;
              margin-left: -5px;
              border-width: 5px;
              border-style: solid;
              border-color: #555 transparent transparent transparent;
            }
            
            .results:hover .tooltiptext {
              visibility: visible;
              opacity: 1;
            }
            .fa {
                color: orange;
            }
            input,select {
             background-color: #E0E0E0;
            }
        </STYLE>
    </HEAD>
    <BODY>
<CENTER>
<FORM>
<TABLE BORDER=0 width=600 style="background-color: #E0E0E0;">
    <TR>
        <TD colspan=2 align=center bgcolor="#ffc457">
            pvpoke2alerts
        </TD>
    </TR>
    <TR>
        <TD valign=top class="block"><BR>
<?php echo $selectleagues; ?> <i class="fa fa-solid fa-question" onclick="giveHelp('selectleague');"></i><BR>
    <TABLE border=0 width=100% cellpadding=0  cellspacing=0 style="border: none;"><TR><TD>
<input type=text id="cmdchar" name="cmdchar" value="<?php echo $defaultchar; ?>" style=" width: 40px;"><label> character</label> <i class="fa fa-solid fa-question" onclick="giveHelp('cmdchar');"></i><BR>
<input type=number id="pvpokerank" name="pvpokerank" value="100" style=" width: 40px;"><label> pvpoke rankings</label> <i class="fa fa-solid fa-question" onclick="giveHelp('pvpokerank');"></i><BR>
<input type=number id="monranks" name="monranks" value="20" style=" width: 40px;"><label id="monrankslabel"> # of each mon</label> <i class="fa fa-solid fa-question" onclick="giveHelp('monranks');"></i><BR>
<input type="text" name="otherinfo" id="otherinfo" placeholder="other info(e.g. d200)"> <i class="fa fa-solid fa-question" onclick="giveHelp('otherinfo');"></i><BR>
</TD><TD width=100 >
    <svg onclick="ProccessAlerts();" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width=70px>
        <!--! Font Awesome Pro 6.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path stroke="black" stroke-width="20" stroke-linecap="round" fill="#ffc457" d="M512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM176 168V344C176 352.7 180.7 360.7 188.3 364.9C195.8 369.2 205.1 369 212.5 364.5L356.5 276.5C363.6 272.1 368 264.4 368 256C368 247.6 363.6 239.9 356.5 235.5L212.5 147.5C205.1 142.1 195.8 142.8 188.3 147.1C180.7 151.3 176 159.3 176 168V168z"/></svg>
</TD></TR></TABLE>
<HR width=70%>
<label><input type="checkbox" name="hideshadow" id="hideshadow" checked>Hide Shadows</label> <i class="fa fa-solid fa-question" onclick="giveHelp('hideshadow');"></i><BR>
<label><input type="checkbox" name="hidelegendary" id="hidelegendary" checked>Hide Legendary</label> <i class="fa fa-solid fa-question" onclick="giveHelp('hidelegendary');"></i><BR>
<label><input type="checkbox" name="combine" id="combine" checked>Combine non forms</label> <i class="fa fa-solid fa-question" onclick="giveHelp('combine');"></i><BR>
<!--<CENTER><button type=button onclick="ProccessAlerts();">Process</button></CENTER><BR>-->
<!--<label id="helplabel" style="background-color: #ff9800;display: inline-block; width: 100%;height: 100px;"></label>-->
<textarea readonly id="helplabel" style="background-color: #ffc457;width: 100%;resize: none;height:130;" onclick="$(this).text('<?php echo $defaulthelp; ?>');"><?php echo $defaulthelp; ?></textarea>
        </TD>
        <TD width=150>
<?php echo $selectcups; ?>
        </TD>
    </TR><TR>
        <TD colspan=2>
<DIV class="results">
    <i class="fa fa-solid fa-copy" onclick="copyAlerts();"><span class="tooltiptext" id="tooltipcopy">Copy to clipboard</span></i>
    <textarea id="displayalerts" name="displayalerts" rows="30" width="100%" style="width: 100%; max-width: 100%;resize: none;background-color: #F8F8F8;"><?php echo $debug; ?></textarea>
</DIV>
        </TD>
    </TR>
    <TR>
        <TD colspan=2 align=center bgcolor="#ffc457">
            Data used in this tool is from the <A HREF="https://pvpoke.com">PvPoke website</A> and their <A HREF="https://github.com/pvpoke/">Github repo</A>.
        </TD>
    </TR>
</TABLE>
</FORM>
    </BODY>
</CENTER>
    
<SCRIPT>
var cuplistmons = [];
var legendaries = ["Mewtwo","Moltres","Articuno","Zapdos","Mew","Lugia","Ho-Oh","Entei","Suicune","Raikou","Celebi","Regirock","Regice","Registeel","Latias","Latios","Groudon","Kyogre","Rayquaza","Jirachi","Deoxys","Dialga","Palkia","Giratina","Uxie","Mesprit","Azelf","Cresselia","Heatran","Regigigas","Phione","Manaphy","Darkrai","Shaymin","Arceus","Cobalion","Terrakion","Virizion","Tornadus","Thundurus","Landorus","Reshiram","Zekrom","Kyurem","Victini","Keldeo","Meloetta","Genesect","Xerneas","Yveltal","Zygarde","Diancie ","Hoopa","Volcanion","Type: Null","Silvally","Tapu Koko","Tapu Lele","Tapu Bulu","Tapu Fini","Cosmog","Cosmoem","Solgaleo","Lunala","Necrozma","Nihilego","Buzzwole","Pheromosa","Xurkitree","Celesteela","Kartana","Guzzlord","Poipole","Naganadel","Stakataka","Blacephalon","Magearna","Marshadow","Zeraora","Meltan","Melmetal","Zacian","Zamazenta","Eternatus","Kubfu","Urshifu","Regieleki","Regidrago","Galarian Articuno","Galarian Moltres","Galarian Zapdos","Glastrier","Spectrier","Calyrex","Zarude"];
function myFunction(which) {
  var elements = document.getElementsByClassName("checkbox");
  var framename = '';
  for(var i = 0; i < elements.length; i++) {
    framename = $(elements[i]).attr('name');
    if ($(elements[i]).hasClass(which)) {
        $(elements[i]).prop("disabled", false);
    } else {
        // console.log(framename);
        $(elements[i]).prop("disabled", true);
        $(elements[i]).prop('checked', false);
    }
  }
  if (which=="10000") {
      $('#monranks').val("90"); 
      $('#monrankslabel').text(" min iv"); 
  } else {
      $('#monranks').val("20"); 
      $('#monrankslabel').text(" # of each mon"); 
  }
}

async function ProccessAlerts() {
  var elements = document.getElementsByClassName("checkbox");
  $('#displayalerts').val("");
  var thevalue = "-";
  var alertarray = [];
  pvpokerank = $('#pvpokerank').val();
  otherinfo = $('#otherinfo').val();
  monranks = $('#monranks').val();
  cmdchar = $('#cmdchar').val();
  leaguename = $('#league').val();
  league="";
  combinednames = "";
  hideshadow = $('#hideshadow').is(':checked');
  hidelegendary = $('#hidelegendary').is(':checked');
  combine = $('#combine').is(':checked');
  if (leaguename == "500") { league="little"; }
  if (leaguename == "1500") { league="great"; }
  if (leaguename == "2500") { league="ultra"; }
  if (leaguename == "10000") { league="iv"; }
  for(var i = 0; i < elements.length; i++) {
    cupname = $(elements[i]).attr('name');
    if ($(elements[i]).is(':checked') && $(elements[i]).is(':enabled')) {
        if (! (cupname+leaguename in cuplistmons)) {
            await $.getJSON(cuplist[cupname+leaguename], function(data) {
                searchlength = pvpokerank;
                if (data.length < pvpokerank) { searchlength = data.length; }
                for (var j = 0; j < searchlength; j++){
                    alertarray.push(data[j].speciesName)
                }
            });
        }
    }
  }
  
      
  let uniquealerts = [...new Set(alertarray)];
  const regex = new RegExp("(?<name>[^(]+)(\\((?<form>[^\)]+)\\))?")
  
  for (var k = 0; k < uniquealerts.length; k++){
    monname = uniquealerts[k];
    const m = monname.match(regex)
    const form = m.groups.form
    const name = m.groups.name
    if (hideshadow && (form == 'Shadow')) { continue }
    if (hidelegendary && (legendaries.indexOf(name) > -1)) { continue }
    pvpcmd = league + ":" + monranks;
    trackcmd = `${cmdchar}track ${name}${form ? `form:${form}` : ''} ${pvpcmd}${otherinfo ? ` ${otherinfo}` : ''}`
    if (!combine || form){ 
        $('#displayalerts').val($('#displayalerts').val() + trackcmd +"\n"); 
        continue   
    }
        
  combinednames+=`${name} `;
  }
  $('#displayalerts').val($('#displayalerts').val() + `${cmdchar}track ${combinednames}${pvpcmd}${otherinfo ? ` ${otherinfo}` : ''}`); 
}

function copyAlerts() {
  var copyText = document.getElementById("displayalerts");
  copyText.select();
  copyText.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(copyText.value);
  
  var tooltip = document.getElementById("tooltipcopy");
  tooltip.innerHTML = "Copied to clipboard.";
  $("html, body").animate({ scrollTop: 0 }, "slow");
  $("#displayalerts").blur(); 
}

function outFunc() {
  var tooltip = document.getElementById("tooltipcopy");
  tooltip.innerHTML = "Copy to clipboard";
}

function giveHelp(which) {
    helptext=which;
    switch(which) {
        case 'selectleague':
            helptext = "select which PVP league that you want alerts for. each league is only available in certain cups so the list to the right will only allow cups that are available.";
            break;
        case 'cmdchar':
            helptext = "character should be set to the command character that your poracle bot uses.\nfor example: ! ~ @ $ .";
            break;
        case 'pvpokerank':
            helptext = "pvpoke rank should be set to the number of pokemon you wanted listed from the pvpoke website.\nfor example, if you are interested in the top 100 pokemon you would enter 100 here.";
            break;
        case 'monranks':
            helptext = "# of each mon is how many of the top ranks of each pokemon are you interested in? rank 1 might be 8/15/15; rank 2 could be 11/15/15, rank 3 7/15/15, rank 4 10/15/15 etc. So you would enter how many of those ranks you are interested for each pokemon.";
            break;
        case 'otherinfo':
            helptext = "otherinfo can be anything else you would like to the end of the track command.\nfor example, if you want to add distance you could enter: d200";
            break;
        case 'hideshadow':
            helptext = "hide shadows will remove shadow pokemon that are in the pvpoke list from showing in the alert list below since shadow pokemon are not wild.";
            break;
        case 'hidelegendary':
            helptext = "hide legendary will remove legenadary pokemon that are in the pvpoke list from showing in the alert list below since legendary pokemon are not (usually) wild.";
            break;
        case 'combine':
            helptext = "combine non forms will place all pokemon without a form on one line, which poracle supports. this can help with message size limits if you have a large list.";
            break;
    }
  $("#helplabel").text(helptext);
}

$( document ).ready(function() {
    myFunction('1500');
    $("#league").val('1500');
});
</SCRIPT>
</HTML>

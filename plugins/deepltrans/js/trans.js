$("#deepltrans_bersetzungen_jetzt_starten_bvar").click(function(){
    console.log("HU");
    var html="";
    var text = "";
    var responsedata_dat ="";
    //text = "ready";
    let i;
   for (i=0; i<=1; i++ )
    {
        responsedata = ajaxcall(i);
        responsedata.success(function (data) {
            console.log(data)
            $("#startresult").append(data)
        });
        console.log(i)
    }
    console.log("fertig")
    return false;
});

function ajaxcall(text="")
{
    return $.ajax({
        method: "GET",
        url: "./plugin.php?menuid=1193&template=deepltrans/templates/bersetzungen_backend.html&ajax=true&ajax_count="+text,
        cache: false
    });
}

$( document ).ajaxStop(function() {
    $( "#loaded" ).show();
});
console.log("da");
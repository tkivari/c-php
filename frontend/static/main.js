$(document).ready(function() {

    console.log('here');
    
    var DownloadDirector = function() {};

    DownloadDirector.prototype = {
        downloadFile: function(filename) {
            var downloader = this;

            if (!filename) {
                this.displayError('you must enter a valid file name.')
            }
            var data = {
                file: $("#filename").val()
            }

            $.post("/index.php", data, function(result){
                $("#error").hide();
                $("#success").html(result.message);
                $("#success").slideDown("slow");
            }).fail(function(error){
                downloader.displayError(error.responseJSON.errors);
            });
        },

        displayError: function(message) {
            $("#success").hide();
            $("#error").html(message);
            $("#error").slideDown("slow");
        }
    };

    var downloader = new DownloadDirector();

    $("#downloadButton").on("click", function() {
        var filename = $("#filename").val();
        downloader.downloadFile(filename);
    });

});
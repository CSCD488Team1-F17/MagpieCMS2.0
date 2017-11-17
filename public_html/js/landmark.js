var count = 1;
var lids = {};

//appends a new badge to the page when the add badge button is clicked
function append(cid){
    var panel = "<div class=\"panel panel-default\">" +
                "<div class=\"panel-body text-center\" style=\"background-color: #40AAF2\" data-toggle=\"collapse\" data-target=#"+ count +">Badge "+count+"</div>" +
            "</div>";

    var collapseable = "<div id="+ count +" class=\"collapse\">" +
                        "<h4 style=\"color:#40AAF2\"><strong>NAME</strong></h4>" +
                        "<div></div>" +
                        "<label>What is this item or location called?</label>" +
                        "<input type=\"text\" class=\"form-control\" id=\"name"+count+"\"></input>" +
                        "<div style=\"padding: 15px\"></div>" +
                        "<h4 style=\"color:#40AAF2\"><strong>LOCATION</strong></h4>" +
                        "<div></div>" +
                        "<label>To locate this item, click to drop a pin on the map or enter the latitude and longitude.</label>" +
                        "<div id=\"map"+count+"\" style=\"width:100%;height:500px;\"></div>" +
                        "<div style=\"padding: 15px\"></div>" +
                        "<div class=\"row\">" +
                            "<div class=\"col-md-6\">" +
                                "<div class=\"form-group\">" +
                                    "<label for=\"Latatude\">Landmark Lat:</label>" +
                                    "<input type=\"text\" class=\"form-control\" id=\"lat"+count+"\">" +
                                "</div>" +
                            "</div>" +
                            "<div class=\"col-md-6\">" +
                                "<div class=\"form-group\">" +
                                    "<label for=\"Longitude\">Landmark Long:</label>" +
                                    "<input type=\"text\" class=\"form-control\" id=\"long"+count+"\">" +
                                "</div>" +
                            "</div>" +
                        "</div>" +
                        "<div style=\"padding: 15px\"></div>" +
                        "<h4 style=\"color:#40AAF2\"><strong>IMAGE</strong></h4>" +
                        "<div></div>" +
                        "<label>Upload a photograph of the item scavengers are looking for.</label>" +
                        "<div class=\"row\">" +
                            "<div class=\"col-md-9\">" +
                                "<textarea class=\"form-control\" rows=\"1\" id=\"picname"+count+"\" readonly></textarea>" +
                            "</div>" +
                            "<div class=\"col-md-3\">" +
                                "<label class=\"btn btn-default btn-file\" style=\"border-radius: 17px; background: #40AAF2; padding-left: 30px; padding-right: 30px;\">Browse" +
                                    "<input id=\"pic"+count+"\" type=\"file\" name=\"newfile\" style=\"display: none;\" onchange=\"setNameImg(this.value, "+count+");\">" +
                                "</label>" +
                            "</div>" +
                        "</div>" +
                        "<div style=\"padding: 15px\"></div>" +
                        "<h4 style=\"color:#40AAF2\"><strong>BADGE ICON</strong></h4>" +
                        "<div></div>" +
                        "<label>Upload a PNG icon to represent this item or location.</label>" +
                        "<div class=\"row\">" +
                            "<div class=\"col-md-9\">" +
                                "<textarea class=\"form-control\" rows=\"1\" id=\"logoname"+count+"\" readonly></textarea>" +
                            "</div>" +
                            "<div class=\"col-md-3\">" +
                                    "<label class=\"btn btn-default btn-file\" style=\"border-radius: 17px; background: #40AAF2; padding-left: 30px; padding-right: 30px;\">Browse" +
                                    "<input id=\"logo"+count+"\" type=\"file\" name=\"newfile\" style=\"display: none;\" onchange=\"setNameLogo(this.value, "+count+");\">" +
                                    "</label>" +
                            "</div>" +
                        "</div>" +
                        "<div style=\"padding: 15px\"></div>" +
                        "<h4 style=\"color:#40AAF2\"><strong>DESCRIPTION</strong></h4>" +
                        "<div></div>" +
                        "<label>Please provide a brief description of your collection. Description can include information such as: general location, types of badges to expect, purpose, etc.</label>" +
                        "<textarea class=\"form-control\" rows=\"6\" id=\"description"+count+"\"></textarea>" +
                        "<div style=\"padding: 15px\"></div>" +
                        "<h4 style=\"color:#40AAF2\"><strong>OBJECT CREATOR(optional)</strong></h4>" +
                        "<div></div>" +
                        "<label>If the location or item where created by an artist, architect, etc, here's your chance to give them the credit they deserve.</label>" +
                        "<textarea class=\"form-control\" rows=\"1\" id=\"creator"+count+"\"></textarea>" +
                        "<div style=\"padding: 15px\"></div>" +
                        "<h4 style=\"color:#40AAF2\"><strong>URL(optional)</strong></h4>" +
                        "<div></div>" +
                        "<label>Which site can the users visit to learn more about this object or location.</label>" +
                        "<textarea class=\"form-control\" rows=\"1\" id=\"url"+count+"\"></textarea>" +
                        "<div style=\"padding: 15px\"></div>" +
                    "</div>" +
                "</div>";
    $("#badgeContainer").append(panel);
    $("#badgeContainer").append(collapseable);
    myMap(count);
    count++;
    $('.collapse').on('shown.bs.collapse', function () {
        myMap(this.id);
    });
    upload(cid);
}
//called when the page loads and populates the collections badges
function onLoad(num){
    for(i = 0; i < num; i++){
        var panel = "<div class=\"panel panel-default\">" +
                "<div class=\"panel-body text-center\" style=\"background-color: #40AAF2\" data-toggle=\"collapse\" data-target=#"+ count +">Badge "+count+"</div>" +
            "</div>";

        var collapseable = "<div id="+ count +" class=\"collapse\">" +
                        "<h4 style=\"color:#40AAF2\"><strong>NAME</strong></h4>" +
                        "<div></div>" +
                        "<label>What is this item or location called?</label>" +
                        "<input type=\"text\" class=\"form-control\" id=\"name"+count+"\"></input>" +
                        "<div style=\"padding: 15px\"></div>" +
                        "<h4 style=\"color:#40AAF2\"><strong>LOCATION</strong></h4>" +
                        "<div></div>" +
                        "<label>To locate this item, click to drop a pin on the map or enter the latitude and longitude.</label>" +
                        "<div id=\"map"+count+"\" style=\"width:100%;height:500px;\"></div>" +
                        "<div style=\"padding: 15px\"></div>" +
                        "<div class=\"row\">" +
                            "<div class=\"col-md-6\">" +
                                "<div class=\"form-group\">" +
                                    "<label for=\"Latatude\">Landmark Lat:</label>" +
                                    "<input type=\"text\" class=\"form-control\" id=\"lat"+count+"\">" +
                                "</div>" +
                            "</div>" +
                            "<div class=\"col-md-6\">" +
                                "<div class=\"form-group\">" +
                                    "<label for=\"Longitude\">Landmark Long:</label>" +
                                    "<input type=\"text\" class=\"form-control\" id=\"long"+count+"\">" +
                                "</div>" +
                            "</div>" +
                        "</div>" +
                        "<div style=\"padding: 15px\"></div>" +
                        "<h4 style=\"color:#40AAF2\"><strong>IMAGE</strong></h4>" +
                        "<div></div>" +
                        "<label>Upload a photograph of the items scavengers are looking for.</label>" +
                        "<div class=\"row\">" +
                            "<div class=\"col-md-9\">" +
                                "<textarea class=\"form-control\" rows=\"1\" id=\"picname"+count+"\" readonly></textarea>" +
                            "</div>" +
                            "<div class=\"col-md-3\">" +
                                "<label class=\"btn btn-default btn-file\" style=\"border-radius: 17px; background: #40AAF2; padding-left: 30px; padding-right: 30px;\">Browse" +
                                    "<input id=\"pic"+count+"\" type=\"file\" name=\"newfile\" style=\"display: none;\" onchange=\"setNameImg(this.value, "+count+");\">" +
                                "</label>" +
                            "</div>" +
                        "</div>" +
                        "<div style=\"padding: 15px\"></div>" +
                        "<h4 style=\"color:#40AAF2\"><strong>BADGE ICON</strong></h4>" +
                        "<div></div>" +
                        "<label>Upload a PNG icon to represent this item or location.</label>" +
                        "<div class=\"row\">" +
                            "<div class=\"col-md-9\">" +
                                "<textarea class=\"form-control\" rows=\"1\" id=\"logoname"+count+"\" readonly></textarea>" +
                            "</div>" +
                            "<div class=\"col-md-3\">" +
                                    "<label class=\"btn btn-default btn-file\" style=\"border-radius: 17px; background: #40AAF2; padding-left: 30px; padding-right: 30px;\">Browse" +
                                    "<input id=\"logo"+count+"\" type=\"file\" name=\"newfile\" style=\"display: none;\" onchange=\"setNameLogo(this.value, "+count+");\">" +
                                    "</label>" +
                            "</div>" +
                        "</div>" +
                        "<div style=\"padding: 15px\"></div>" +
                        "<h4 style=\"color:#40AAF2\"><strong>DESCRIPTION</strong></h4>" +
                        "<div></div>" +
                        "<label>Please provide a brief description of your collection. Description can include information such as: general location, types of badges to expect, purpose, etc.</label>" +
                        "<textarea class=\"form-control\" rows=\"6\" id=\"description"+count+"\"></textarea>" +
                        "<div style=\"padding: 15px\"></div>" +
                        "<h4 style=\"color:#40AAF2\"><strong>OBJECT CREATOR(optional)</strong></h4>" +
                        "<div></div>" +
                        "<label>If the location or item where created by an artist, architect, etc, here's your chance to give them the credit they deserve.</label>" +
                        "<textarea class=\"form-control\" rows=\"1\" id=\"creator"+count+"\"></textarea>" +
                        "<div style=\"padding: 15px\"></div>" +
                        "<h4 style=\"color:#40AAF2\"><strong>URL(optional)</strong></h4>" +
                        "<div></div>" +
                        "<label>Which site can the users visit to learn more about this object or location.</label>" +
                        "<textarea class=\"form-control\" rows=\"1\" id=\"url"+count+"\"></textarea>" +
                        "<div style=\"padding: 15px\"></div>" +
                    "</div>" +
                "</div>";



        $("#badgeContainer").append(panel);
        $("#badgeContainer").append(collapseable);
        count++;
    }
}

var marker;
//function used to load the google map
function myMap(element) {
    var mapCanvas = document.getElementById('map'+element);
    var myCenter=new google.maps.LatLng(47.67509743551929,-117.36024856567383);
    var mapOptions = {center: myCenter, zoom: 13};
    var map = new google.maps.Map(mapCanvas, mapOptions);
    google.maps.event.addListener(map, 'click', function(event) {
        placeMarker(map, event.latLng, element);
    });
    google.maps.event.trigger(map, 'resize');
}
//function used by the google map to place a marker
function placeMarker(map, location, element) {
    if(marker){
        marker.setPosition(location);
    }else{
        marker = new google.maps.Marker({
            position: location,
            map: map
        });
    }
    var tempLoc = String(location).substring(1,String(location).length-2).split(",");
    document.getElementById('lat'+element).value = tempLoc[0];
    document.getElementById('long'+element).value = tempLoc[1].substring(1,tempLoc[1].length-1);
    //alert(String(location).split(",")[0]);
    var infowindow = new google.maps.InfoWindow({
        content: 'Latitude: ' + location.lat() + '<br>Longitude: ' + location.lng()
    });
    infowindow.open(map,marker);
}
//function used by append to make sure the new badges are given a proper event handler
function upload(cid){
    $('.collapse').on('hidden.bs.collapse', function () {
        var valid = validate(this.id);
        var id = this.id;
        var name = $("#name" + this.id).val();
        var description = $("#description" + this.id).val();
        var lat = $("#lat" + this.id).val();
        var long = $("#long" + this.id).val();
        var file1 = $("#pic" + this.id)[0].files[0];
        var file2 = $("#logo" + this.id)[0].files[0];
        var file1name = $("#picname" + this.id).val();
        var file2name = $("#logoname" + this.id).val();
        var creator = $("#creator" + id).val();
        var url = $("#url" + id).val();
        var cid = cid;
        var formData = new FormData();
        formData.append("cid", cid);
        formData.append("file1", file1, file1name);
        formData.append("file2", file2, file2name);
        formData.append("name", name);
        formData.append("lat", lat);
        formData.append("long", long);
        formData.append("description", description);
        formData.append("creator", creator);
        formData.append("url", url);

        if (valid){
            if(lids[this.id] != -1){
                $.ajax({
                    url:'/database/landmark/update/' + lids[id],
                    type:'post',
                    data:formData,
                    contentType: false,
                    processData: false,
                    success:function(data){
                    }
                });
            }else{
                $.ajax({
                    url:'/database/landmark',
                    type:'post',
                    data:formData,
                    contentType: false,
                    processData: false,
                    success:function(data){
                        lids[id] = data;
                    }
                });
            }
        }
    });
}
//data validation function
function validate(id){
    if($("#name" + id).val() == ""){
        alert("You have not entered a NAME!");
        return false;
    }else if($("#lat" + id).val() == "" || $("#long" + id).val() == ""){
        alert("You have an invalid LATITUDE or LONGITUDE");
        return false;
    }else if($("#description" + id).val() == ""){
        alert("You have not entered a DESCRIPTION!");
        return false;
    }else if(!($("#picname" + id).val().endsWith(".png"))){
        alert("You have to upload a .png image file!");
        return false;
    }else if(!($("#logoname" + id).val().endsWith(".png"))){
        alert("You have to upload a .png badge file!");
        return false;
    }else{
        return true;
    }
}

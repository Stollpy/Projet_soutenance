$(document).ready(function(){
    $("#LoadMore").click(function(){
        $.ajax({
            url: "/load",
            type: "GET",
            dataType: 'json',
            async: true,
            headers: {
                "X-Requested-With":"XMLHttpRequest"
            },
            success : //alert('Success'),
                function(response){
                    console.log(response);

                    for(i = 0; i < 4; i++){
                        shop = response[i];
                        var e = $('<div class="col-lg-3 mt-2">\n' +
                            '                   <div class="card border-light" style="width: 100%;">\n' +
                            '                       <!-- Bouton favoris à revoir -->\n' +
                            '                       <a href="#"><i class="far fa-heart mj-bookmark-icon"></i></a><a href="" id="routeShop"><img\n' +
                            '                       src="" class="card-img-top" alt="ShopImage" id="Img"></a>\n' +
                            '                       <div class="card-body">\n' +
                            '                           <h5 class="card-title" id="shopName"></h5>\n' +
                            '                           <h6 class="card-subtitle mb-2 text-muted">30 - 20 min</h6>\n' +
                            '                           <p class="card-text">Numéro de SIRET : <span id="siret"></span>.</p>\n' +
                            '                       </div>\n' +
                            '                   </div>\n' +
                            '               </div>');
                        $('#siret', e).html(shop['siret']);
                        $('#shopName', e).html(shop['name']);
                        $('#routeShop', e).href(shop['id']);
                        $('#Img', e).src(shop['picture']);
                        $("#DisplayShop").append(e);
                    }
                },
            error :
                function(xhr, textStatus, errorThrown) {
                    alert('Ajax request failed');
                    console.log(xhr, textStatus, errorThrown);
                }
        })
    })
})

/*
 Framapack est un installeur de logiciels libres.
 Copyright (C) 2009  Simon Leblanc <contact@leblanc-simon.eu>
 
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU Affero General Public License as
 published by the Free Software Foundation, either version 3 of the
 License.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU Affero General Public License for more details.
 
 You should have received a copy of the GNU Affero General Public License
 along with this program.  If not, see http://www.gnu.org/licenses/.
*/

var nb_selected = 0;

/**
 * Affiche la catégorie sélectionnée
 *
 * @param   int   id    l'identifiant de la catégorie à afficher
 * @param   int   nb    le nombre d'application dans la catégorie à afficher
 */
function show(id, nb)
{
  // Si on demande la même catégorie, on sort
  if (current == id) {
    return true;
  }
  
  // On applique le style de la catégorie sélectionnée sur la bonne catégorie
  $('#bloc-category-' + id).addClass('selected');
  $('#bloc-category-' + current).removeClass('selected');
  
  if (nb > nb_item) {
    // si le nombre d'application est plus important dans la catégorie demandée
    // que dans l'ancienne catégorie, on passe la catégorie actuelle en position
    // relative afin que la div s'agrandisse correctement et l'ancienne en position
    // absolue (pour permettre de passer au dessus)
    $('#category_' + id).css('position', 'relative');
    $('#category_' + current).css('position', 'absolute').css('top', '0').css('left', '0');
  } else {
    // si le nombre d'application est moins important dans la catégorie demandée
    // que dans l'ancienne catégorie, on passe la catégorie actuelle en position
    // absolue et l'ancienne en position relative (la taille reste bonne et les
    // catégorie se chevauche)
    $('#category_' + current).css('position', 'relative');
    $('#category_' + id).css('position', 'absolute').css('top', '0').css('left', '0');
  }
  
  if ($.browser.msie) {
    // Tiens c'est cette merde d'IE, on patch vu que les programmeurs
    // de microsoft sont incapables de le faire !
    $('#category_' + id).show();
    $('#category_' + current).hide();
  } else {
    $('#category_' + current).fadeOut('slow');
    $('#category_' + id).fadeIn('slow', function(){$(this).css('position', 'relative');});
  }
  
  current = id;
  nb_item = nb;
}

/**
 * Ajoute une application au panier
 *
 * @param   int     id    l'identifiant de l'application à ajouter au panier
 * @param   string  name  le nom de l'application à ajouter au panier
 */
function addCart(id, name)
{
  updateNbApps('add');
  
  var div = document.createElement('div');
  
  div.setAttribute('class', 'application');
  div.setAttribute('id', 'cart_' + id);
  div.setAttribute('style', 'display:none;');
  div.innerHTML = name + '<img src="images/delete.png" class="delete" onclick="removeCart(' + id +', true);" />';
  
  document.getElementById('cart').appendChild(div);
  $('#cart_' + id).fadeIn('slow');
  
  buildLink();
}

/**
 * Supprime une application du panier
 *
 * @param   int   id    l'identifiant de l'application à supprimer du panier
 * @param   bool        True s'il faut désélectionner la checkbox de l'application
 */
function removeCart(id, uncheck)
{
  // On supprime l'attribut onclick pour eviter de faire bugger l'application
  $('#cart_' + id + ' > img').removeAttr('onclick');
  
  if (uncheck == true) {
    $('#application_' + id).removeAttr('checked');
  }
  $('#cart_' + id).fadeOut('slow', function(){document.getElementById('cart').removeChild(document.getElementById('cart_' + id));updateNbApps('remove');});
  
  buildLink();
}

/**
 * Met à jour le nombre d'applications sélectionnées
 *
 * @param   string    action    'add' ou 'remove' pour indiquer l'action effectuée
 */
function updateNbApps(action)
{
  if (action == 'add') {
    nb_selected++;
  } else if (action == 'remove') {
    nb_selected--;
  }
  
  if (nb_selected > 0) {
    $('#cart').addClass('border');
  } else {
    $('#cart').removeClass('border');
  }
  
  if (nb_selected > 1) {
    text_selection = nb_selected + ' applications';
  } else {
    text_selection = nb_selected + ' application';
  }
  
  $('#nb_apps').text(text_selection);
}


/**
 * Fonction appellé avec le slot sur la checkbox
 * 
 * @param   DOMElement  checkbox    L'element DOM correspondant à la checkbox
 */
function clickCheckbox(checkbox)
{
  var id = $(checkbox).val();
  if ($(checkbox).is(':checked')) {
    addCart(id, $(checkbox).attr('rel'));
  } else {
    removeCart(id);
  }
}


/**
 * Fonction permettant de créer le lien de partage
 */
function buildLink()
{
  var link = '';
  var i = 0;
  
  $(':checkbox:checked').each(function() {
    if (i > 0) {
      link += '-';
    }
    link += $(this).val();
    i++;
  });
  
  if (link != '') {
    link = url_framapack + '?share=' + link;
  }
  
  $('#share_framapack').val(link);
}


//Fonctions executées lorsque la page HTML est chargée entièrement
$(document).ready(function(){
  var click_in_checkbox = false;
  // Ajoute un slot sur les checkbox afin de controler les applications sélectionnées
  $(":checkbox").click(function(){
    click_in_checkbox = true;
  });
  
  // Ajoute un slot sur les liens vers les notices
  //$("a.fiche").fancybox();
  //$(".iframe").colorbox({iframe:true, width:"500px", height:"400px", innerWidth:"500px",initialWidth:"500px"});
  $(".iframe").colorbox({iframe:true,  width: "580", 
      height: "380px", transition: "elastic", scrolling:true, close:"fermer",
      
      onComplete: function () 
          { 
	        //$("#cboxLoadedContent").addClass("musicBgr"); 
		$("#colorbox iframe").css( 
		 { 
		     'width': '580px', 
		     'height': '380px', 
		     'margin': '0px auto',
		     'background-color': '#fff'
		  }); 
	  } 
				       
       }); 
 
  
  // Cache les blocs des catégories qui ne sont pas sélectionnées
  $('.list-applications').map(function(){
    if ($(this).attr('id') != 'category_' + current) {
      $(this).hide();
    }
  });
  
  // Ajoute un slot sur les div des applications afin de controler les applications sélectionnées
  $('.application:not(.information,.checkbox)').click(function(){
    var checkbox = $(this).children('.checkbox').children('input').get(0);
    if (click_in_checkbox == false) {
      checkbox.checked = !checkbox.checked;
    } else {
      click_in_checkbox = false;
    }
    clickCheckbox(checkbox);
  });
  
  // Ajoute un slot sur le click dans le input de l'adresse de partage afin de sélectionner le texte
  $('#share_framapack').click(function(){$(this).select();})
  
  // Ajoute un slot permettant l'affichage d'une explication pour le lien de partage
  $(".share").hover(function() {
    $(this).find(".popup").stop(true, true).animate({opacity: "show", left: "-260"}, "fast");
  }, function() {
    $(this).find(".popup").animate({opacity: "hide", left: "-280"}, "fast");
  });

});
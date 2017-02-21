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
 * Ajoute une application au panier
 *
 * @param   int     id    l'identifiant de l'application à ajouter au panier
 * @param   string  name  le nom de l'application à ajouter au panier
 */
function addCart(id, name)
{
    updateNbApps('add');
    
    $('#application_'+id).parent().parent().addClass('selected');
  
    $('#cart').append(
        '<div class="application" id="cart_' + id + '" style="display:none;">' +
            name +
            '<a href="javascript:void(0)" title="Supprimer '+name+' de la liste" onclick="removeCart(' + id +', true);">'+
                '<span class="fa fa-fw fa-remove text-danger"></span><span class="sr-only">Supprimer '+name+' de la liste</span>'+
            '</a>'+
        '</div>'
    );
  
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
    $('#application_'+id).parent().parent().removeClass('selected');
    
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
        $('#cart').addClass('well');
    } else {
        $('#cart').removeClass('well');
    }
  
    if (nb_selected > 1) {
        text_selection = nb_selected + ' applications';
    } else {
        text_selection = nb_selected + ' application';
    }
  
    $('#nb_apps').text(text_selection);
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
    $('input:checkbox').removeAttr('checked'); // suppression des checkbox mémorisées par le navigateur
    $('#share_framapack').val('');             // et du lien de partage
    
    // Modale pour afficher la fiche du logiciel
    $('a.fiche').on('click', function(){
        var modalTitle = $(this).attr('title');
        var modalIframe = $(this).attr('href');
        $('#modal-pack').remove();
        $('main').after(
            '<div class="modal fade" id="modal-pack" aria-labelledby="modal-packLabel" >'+
                '<div class="modal-dialog modal-lg">'+
                    '<div class="modal-content">'+
                        '<div class="modal-header">'+
                            '<button type="button" class="close" data-dismiss="modal" title="Fermer"><span aria-hidden="true">&times;</span><span class="sr-only">Fermer</span></button>'+
                            '<h1 id="modal-packLabel">'+modalTitle+'</h1>'+
                        '</div>'+
                        '<div class="modal-body">'+
                            '<iframe frameborder="0" src="'+modalIframe+'" ></iframe>'+
                        '</div>'+
                        '<div class="modal-footer">'+
                            '<button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>'
        );
        $('#modal-pack').modal('show');
        $('#modal-pack iframe').css({
            'width':$('#modal-pack .modal-dialog').width()-25,
            'height':($(window).height()-250)
        });
        return false;
    });
    
    // Popover sur le lien de partage
    $('#share-popover').popover();
    
    // Ajout/Suppression des logiciels de la liste et du compteur
    $('input:checkbox').change(function() {
        var checkbox = $(this);
        var id = $(checkbox).val();
        if ($(checkbox).is(':checked')) {
            addCart(id, $(checkbox).attr('rel'));
        } else {
            removeCart(id);
        }
    });
    // Ajoute un slot sur le click dans le input de l'adresse de partage afin de sélectionner le texte
    $('#share_framapack').click(function(){$(this).select();})
});

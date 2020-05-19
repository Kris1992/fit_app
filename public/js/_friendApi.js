import { getStatusError } from './_apiHelper.js';

'use strict';

(function(window, $, Swal)
{

    const responses = [
        'accept',
        'reject'
    ];

    class FriendApi
    {
        constructor($wrapper)
        {
            this.$wrapper = $wrapper;
            this.handleDocumentLoad();
           
            this.$wrapper.on(
                'click',
                FriendApi._selectors.sendInvitationLink,
                this.handleSendInvitation.bind(this)
            );
            this.$wrapper.on(
                'click',
                FriendApi._selectors.sendResponseLink,
                this.handleSendResponse.bind(this)
            );

        }

        static get _selectors() {
            return {
                sendInvitationLink: '.js-friend',
                sendResponseLink: '.js-friend-response',
                cardWrapper: '.js-card'
            }
        }

        handleDocumentLoad() { 
            this.setTooltips();
        }

        handleSendInvitation(event) {
            event.preventDefault();
            var $link = $(event.currentTarget);

            if($link.find('span').hasClass("text-green")) {
                this.showErrorMessage('You already invited this person.')
            }

            this.sendInvitation($link.attr('href')).then((data) => {
                $link.find('span').addClass('text-green');
            }).catch((errorData) => {
                this.showErrorMessage(errorData.title);
            });
        }

        handleSendResponse(event) {
            event.preventDefault();
            var $link = $(event.currentTarget);
            var action = $link.data('action');
            
            if (responses.includes(action)) {
                var actionData = {
                    status: action
                };
                this.sendResponse(actionData, $link.attr('href')).then((data) => {
                    $link.closest(FriendApi._selectors.cardWrapper).fadeOut('normal', () => {
                        $link.closest(FriendApi._selectors.cardWrapper).remove();
                    });
                }).catch((errorData) => {
                    this.showErrorMessage(errorData.title);
                });
            } else {
                this.showErrorMessage('Cannot send that data.');
            }
        }

        setTooltips() {
            $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});
            $('[data-toggle="tooltip"]').on('click', function () {
                $(this).tooltip('hide')
            });
        }

        sendInvitation(url) {
            return new Promise(function(resolve, reject) { 
                $.ajax({
                    url,
                    method: 'GET',
                }).then((data) => {
                    resolve(data);
                }).catch((jqXHR) => {
                    let errorData = getStatusError(jqXHR);
                    if(errorData === null) {
                        errorData = JSON.parse(jqXHR.responseText);
                    }
                    reject(errorData);
                });
            });
        }

        sendResponse(actionData, url) {
            return new Promise(function(resolve, reject) { 
                $.ajax({
                    url,
                    method: 'POST',
                    data: JSON.stringify(actionData)
                }).then((data) => {
                    resolve(data);
                }).catch((jqXHR) => {
                    let errorData = getStatusError(jqXHR);
                    if(errorData === null) {
                        errorData = JSON.parse(jqXHR.responseText);
                    }
                    reject(errorData);
                });
            });
        }

        showErrorMessage(errorMessage) {
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: `${errorMessage}`,
            });
        }

    }

    window.FriendApi = FriendApi;

})(window, jQuery, Swal);

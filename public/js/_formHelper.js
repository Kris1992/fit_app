/**
 * initCollections Inits Wrapper with collection forms cards 
 * @return {[void]} 
 */
export function initCollections() {
    var $collectionWrapper;
    var $addNewSetButton = $(`<a href="#"" class="btn btn-info my-2">Add set</a>`);

    $collectionWrapper = $('#workout_sets');
    $collectionWrapper.append($addNewSetButton);
    $collectionWrapper.data('counter', $collectionWrapper.find('.card-js').length)
    $collectionWrapper.find('.card-js').each(() => {   
        addRemoveButton($(this));
    });

    $addNewSetButton.click((event) => {
        event.preventDefault();
        addNewForm($collectionWrapper, $addNewSetButton);
    });
}

/**
 * addNewForm Add new Collection form card
 * @param $collectionWrapper Collection form wrapper
 * @param $addNewSetButton Add new set button handler
 */
export function addNewForm($collectionWrapper, $addNewSetButton) {
    var newForm = $collectionWrapper.data('prototype');
    var counter = $collectionWrapper.data('counter');

    newForm = newForm.replace(/__name__/g, counter);

    var $card = $(`
        <div class="card my-2 card-js">
            <div class="card-header bg-info text-white">
                <strong>SET ${counter}</strong>
            </div>
        </div>`
    );
    var $cardBody = $(`<div class="card-body"></div>`).append(newForm);

    $card.append($cardBody);

    $collectionWrapper.data('counter', counter+1);

    addRemoveButton($card);

    $addNewSetButton.before($card);
}

/**
 * addRemoveButton  Add remove button to collection form card
 * @param $setWrapper Card with collection forms which needs remove button 
 */
export function addRemoveButton($setWrapper) {
    var $removeButton = $(`<a href="#" class="btn btn-danger remove-js">Remove set</a>`);
    var $footerCard = $(`<div class="card-footer bg-secondary text-center"></div>`).append($removeButton);
    $setWrapper.append($footerCard);
}

/**
 * handleRemoveButtonClick Handle remove button click event
 * @param event
 */
export function handleRemoveButtonClick(event)
{
    event.preventDefault();
    $(event.target).parents('.card-js').slideUp("normal").promise().done(function() {
        $(this).remove();
    });
}
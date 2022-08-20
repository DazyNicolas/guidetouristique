export default function flashy(message, link) {
    const template = $($('#flashy-template').html())
    $('.flashy').remove()
    template
        .find('.flashy__body')
        .html(message)
        .attr('href', link || '#')
        .end()
        .appendTo('body')
        .hide()
        .fadeIn(300)
        .delay(2800)
        .animate(
            {
                marginRight: '-100%'
            },
            300,
            'swing',
            function() {
                $(this).remove()
            }
        )
}

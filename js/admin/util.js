
/**
 * This is for the product options in admin/product/edit [options tab]
 *
 */
function hE(elementTo)
{
    this.name   = '';
    this.id     = '';
    this.uri    = '';
    this.type   = 'div';
    this.text   = '';
    this.title  = '';
    this.attatchMethod = 'append';

    this.speak =    function() { alert("yes I'm here.."); };
    this.build = _build( this, elementTo );
}

function _build( _hE, elementTo )
{
    if(_hE.attatchMethod == 'append')
    {
        jQuery('<'+_hE.type+'/>', {
            id: _hE.id,
            href: _hE.uri,
            title: _hE.title,
            rel: 'external',
            text: _hE.text,
        }).appendTo(elementTo);
    }
}
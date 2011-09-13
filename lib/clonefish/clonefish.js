
// array to store elements with visible error divs attached
// so we can clean up the divs later
errorDivsVisible = Array();

// ----------------------------------------------------------------------------
function clonefishSelectDateStoredFormat( form, field, format, padding ) {

  partMap = new Array();
  partMap['%Y'] = 'year';
  partMap['%M'] = 'month';
  partMap['%D'] = 'day';
  partMap['%h'] = 'hour';
  partMap['%m'] = 'min';
  partMap['%s'] = 'sec';
  out = '';

  for ( i = 0; i < format.length; i++ ) {

    part = format.substr( i, 2 );
    switch ( part ) {
      case '%Y':
      case '%M':
      case '%D':
      case '%h':
      case '%m':
      case '%s':
        elementname = field + partMap[ part ];
        value       = form[ elementname ][ form[ elementname ].selectedIndex ].value;
        if ( padding || ( part == '%h' ) || ( part == '%m' ) || ( part == '%s' ) ) {
          padLength = part == '%Y' ? 4 : 2;
          while ( value.length < padLength )
            value = '0' + value;
        }
        out += value;
        i++;
        break;
      default:
        out += format.charAt(i);
        break;
    }

  }

  form[ field ].value = out;

}

// ----------------------------------------------------------------------------
function clonefishValidDateTime( 
  
  date_input, format, 
  minimum, maximum, 

  less,       lessformat, 
  lesseq,     lesseqformat, 
  greater,    greaterformat, 
  greatereq,  greatereqformat 

) {

// - day and month might be 1 or 2 chars long
// - the separator must be the - sign
// - function checks the followings also:
//   year is 4 digits, 1 <= month <=12, 1 <= days <= days_in_month
//
// - valid examples:    2002-01-2,  2002-1-1, 1951-12-31, 2000-02-29
// - invalid examples:  2002-02-29, 2000-0-1, 200-0-1,    2000-abc

  if ( typeof( date = clonefishGetValidDate( date_input, format ) ) != 'object' )
    return false;

  if ( null != minimum ) {

    minimumdate = new Date( minimum * 1000 );
    if ( d.date < minimumdate ) 
      return false;

  }

  if ( null != maximum ) {
    
    maximumdate = new Date( maximum * 1000 );
    if ( d.date > maximumdate ) 
      return false;

  }

  if ( ( null != less )      && !clonefishDateCompare( d, 'lt',  less,      lessformat ) )
    return false;

  if ( ( null != lesseq )    && !clonefishDateCompare( d, 'leq', lesseq,    lesseqformat ) )
    return false;
  
  if ( ( null != greater )   && !clonefishDateCompare( d, 'gt',  greater,   greaterformat ) )
    return false;

  if ( ( null != greatereq ) && !clonefishDateCompare( d, 'geq', greatereq, greatereqformat ) )
    return false;

  return true;

}

// ----------------------------------------------------------------------------
function clonefishDateCompare( baseDate, operator, otherfieldvalue, otherfieldformat ) {

  otherDate = clonefishGetValidDate( otherfieldvalue, otherfieldformat );
  if ( typeof( otherDate ) != 'object' )
    return false;

  switch ( operator ) {
    case 'lt':  return baseDate.date <  otherDate.date; break;
    case 'leq': return baseDate.date <= otherDate.date; break;
    case 'gt':  return baseDate.date >  otherDate.date; break;
    case 'geq': return baseDate.date >= otherDate.date; break;
    default: alert('unimplemented date comparison operator'); return false; break;
  }

}

// ----------------------------------------------------------------------------
function clonefishGetValidDate( date_input, format ) {

  // create a validation regexp

  formatcompiled = '^' + format + '$' ;
  formatcompiled = formatcompiled.replace( 'YYYY', '([0-9]{4})' );
  formatcompiled = formatcompiled.replace( 'YY', '([0-9]{2})' );
  formatcompiled = formatcompiled.replace( 'MM', '([0-9]{2})' );
  formatcompiled = formatcompiled.replace( 'M', '([0-9]{1,2})' );
  formatcompiled = formatcompiled.replace( 'DD', '([0-9]{2})' );
  formatcompiled = formatcompiled.replace( 'D', '([0-9]{1,2})' );
  formatcompiled = formatcompiled.replace( 'hh', '([0-9]{2})' );
  formatcompiled = formatcompiled.replace( 'h', '([0-9]{1,2})' );
  formatcompiled = formatcompiled.replace( 'mm', '([0-9]{2})' );
  formatcompiled = formatcompiled.replace( 'm', '([0-9]{1,2})' );
  formatcompiled = formatcompiled.replace( 'ss', '([0-9]{2})' );
  formatcompiled = formatcompiled.replace( 's', '([0-9]{1,2})' );
  regexp = new RegExp( formatcompiled );
  result = regexp.exec( date_input );

  if ( !result ) 
    // format itself failed
    return false;

  // if syntax is ok, the check the date
  // semantically

  // find element indexes and make a match with 
  // the regexp indexes

  indexes = Array(
    new Array( format.indexOf( 'YYYY' ), 'year' ),
    new Array( format.indexOf( 'M' ), 'month' ),
    new Array( format.indexOf( 'D' ), 'days' ),
    new Array( format.indexOf( 'h' ), 'hour' ),
    new Array( format.indexOf( 'm' ), 'min' ),
    new Array( format.indexOf( 's' ), 'sec' )
  );

  indexes.sort( clonefishIndexCompare );

  regexindex = new Array();
  counter    = 1;

  for ( i = 0; i < indexes.length; i++ )
    if ( indexes[ i ][ 0 ] != -1 ) {
      regexindex[ indexes[ i ][ 1 ] ] = counter;
      counter++;
    }

  // at last... we know the regexp-indexes of 
  // the date components (eg 'YYYY' is Regex.$1 )
  // and so on

  d = {
    year:  result[ regexindex[ 'year'  ] ],
    month: result[ regexindex[ 'month' ] ],
    days:  result[ regexindex[ 'days'  ] ],
    hour:  result[ regexindex[ 'hour'  ] ],
    min:   result[ regexindex[ 'min'   ] ],
    sec:   result[ regexindex[ 'sec'   ] ]
  };

  daysOfMonth = new Array( 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 );
  if ( ( null != d.year ) && ( ( d.year % 4 ) == 0 ) )
    daysOfMonth[1] = 29;

  if (
       ( ( null != d.month ) && ( ( d.month <= 0 ) || ( d.month > 12 ) ) ) ||
       ( ( null != d.days )  && ( ( d.days <= 0 )  || ( d.days > 31 ) ) )  ||
       ( ( d.month && ( d.days > daysOfMonth [ d.month - 1 ] ) ) ) ||
       ( ( null != d.hour )  && ( ( d.hour < 0 )   || ( d.hour > 23 ) ) )  ||
       ( ( null != d.min )   && ( ( d.min < 0 )    || ( d.min > 59 ) ) )   ||
       ( ( null != d.sec )   && ( ( d.sec < 0 )    || ( d.sec > 59 ) ) )
     )
    return false;

  if ( null != d.month ) 
    month_num = d.month - 1;
  else
    month_num = null;

  d.date = new Date( 
    clonefishUndefined( d.year ), 
    month_num, 
    clonefishUndefined( d.days ), 
    clonefishUndefined( d.hour ), 
    clonefishUndefined( d.min ), 
    clonefishUndefined( d.sec ) 
  );

  return d;

}

// ----------------------------------------------------------------------------
function clonefishIndexCompare( item1, item2 ) {

  if ( item1[ 0 ] < item2[ 0 ] )
    return -1;

  if ( item1[ 0 ] > item2[ 0 ] )
    return 1;

  return 0;

}

// ----------------------------------------------------------------------------
function clonefishUndefined( value ) {

  return typeof( value ) == "undefined" ? null : value;

}

// ----------------------------------------------------------------------------
function clonefishErrors() {

  // class to encapsulate methods for displaying error messages.
  // handles alert() and innerHTML operations.

  // properties
  this.useAlert                    = false;
  this.useHTML                     = false;
  this.empty                       = true; // set by the class as an overall result flag

  this.errorMessages               = new Array();
  this.messageContainerLayout      = '%s';
  this.messageLayout               = '%s<br />';
  this.alertMessageContainerLayout = '%s';
  this.alertMessageLayout          = " - %s\n";

  // "Javascript Does Not Support Associative Arrays"
  // http://www.hunlock.com/blogs/Mastering_Javascript_Arrays
  // 
  // that's why we need the indexmap: this way we can avoid
  // later using the for...in construct, which also conflicts
  // prototype.js
  this.inputnameMap                = new Array();

  // to avoid the same messages stored multiple times
  this.messageMap                  = new Array();

  // methods
  this.addIf    = clonefishErrorsAddIf;
  this.add      = clonefishErrorsAdd;
  this.go       = clonefishErrorsGo;
  this.findDiv  = clonefishGetElement;

  // clean up error divs displayed by previous calls
  for ( i = 0; i < errorDivsVisible.length; i++ ) {

    errordiv = this.findDiv( errorDivsVisible[ i ] );

  if ( errordiv ) {

    errordiv.innerHTML        = '';
    errordiv.style.visibility = 'hidden';
    errordiv.style.display    = 'none';

  }

  }

}

// ----------------------------------------------------------------------------
function clonefishErrorsAddIf( inputname, condition, message ) {

  if ( !condition )
    this.add( inputname, message );

}

// ----------------------------------------------------------------------------
function clonefishErrorsAdd( inputname, message ) {

  // first let's check if this message has already been
  // stored
  for ( i = 0; i < this.messageMap.length; i++ )
    if ( message == this.messageMap[i] )
      return;

  // see note about associative arrays in clonefishErrors()

  // faster and easier to search in the name map 
  // when the names are joined
  inputnames = ',' + this.inputnameMap.join(',') + ',';

  // escaping regular expression for inputs with special characters
  // in their name (eg. [] for array handling in PHP)
  inputnameEscaped = inputname.replace( /([\[\]])/g, '\\$1' );

  // find name in name map

  if ( inputnames.search( ',' + inputnameEscaped + ',' ) == -1 ) {
    index                      = this.inputnameMap.length;
    this.inputnameMap[ index ] = inputname;
  }
  else {

    index = 0;
    for ( i = 0; i < this.inputnameMap.length; i++ ) {
      if ( this.inputnameMap[ i ] == inputname )
        index = i;
    }

  }

  // now store the message where it's needed

  if ( null != this.errorMessages[ index ] ) {

    messageindex = this.errorMessages[ index ]['messages'].length;
    this.errorMessages[ index ]['messages'][ messageindex ] = message;

  }
  else {

    // new input: add array and initialize 2nd dimension
    this.errorMessages[ index ] = new Array();
    this.errorMessages[ index ]['inputname'] = inputname;
    this.errorMessages[ index ]['messages']  = Array( message );

  }

  // store message to avoid repeating messages
  this.messageMap[ this.messageMap.length ] = message;

}

// ----------------------------------------------------------------------------
function clonefishErrorsGo() {

  alertmessage = '';
  this.empty   = true;

  while ( this.errorMessages.length > 0 ) {

    currentElement = this.errorMessages.shift();

    if ( this.useHTML ) {

      errordiv = this.findDiv( 'cf_error' + currentElement['inputname'] );

      if ( null != errordiv ) {

        if ( null != errordiv.innerHTML ) {

          // compile messages in layout
          message = '';

          for ( i = 0; i < currentElement['messages'].length; i++ )
            message =
              message +
              this.messageLayout.replace('%s', currentElement['messages'][ i ] );

          if ( message != '' ) {
            errorDivsVisible[ errorDivsVisible.length ] = errordiv.id;
            errordiv.innerHTML        =
              this.messageContainerLayout.replace('%s', message );
            errordiv.style.visibility = 'visible';
            errordiv.style.display    = 'block';
            this.empty                = false;
          }
          else {
            errordiv.innerHTML        = '';
            errordiv.style.visibility = 'hidden';
            errordiv.style.display    = 'none';
          }

        }
        else
          // no innerHTML support: fallback to simple alert
          this.useAlert = true;

      }
      else
        // HTML element not found by ID: fallback to simple alert
        this.useAlert = true;

    }

    if ( this.useAlert ) {

      for ( i = 0; i < currentElement[ 'messages' ].length; i++ ) {
        alertmessage = 
          alertmessage + 
          this.alertMessageLayout.replace(
            '%s', currentElement['messages'][ i ]
          )
        ;
        this.empty = false;
      }

    }

  }

  if ( !this.empty && this.useAlert && ( alertmessage.length > 0 ) )
    alert( this.alertMessageContainerLayout.replace('%s', alertmessage ) );

}

// ----------------------------------------------------------------------------
function clonefishGetElement( id ) {

  if ( typeof( document.getElementById ) != "undefined" )
    return document.getElementById( id );
  else
    if ( typeof( document.all ) != "undefined" )
      return document.all[ id ];

  return false;

}

// ----------------------------------------------------------------------------
function clonefishGetFieldValue( formname, field, type ) {

  // helper function to return entered/selected values
  // of different input types

  form     = document.forms[ formname ];
  value    = false;
  multiple = Array();

  switch ( type.toLowerCase() ) {

    case 'inputcheckbox':
      
      if ( typeof( form[ field ].value ) != 'undefined' )
        value = form[ field ].checked ? form[ field ].value : false;
      else
        value = form[ field ].checked ? true : false;
      
      return value;

      break;
    
    case 'inputcheckboxdynamic':
      
      // a group of checkboxes  

      inputs = form.getElementsByTagName('input');
      
      for ( i = 0; i < inputs.length; i++ ) {
        
        rE = new RegExp( '^' + field + '\\[.+\\]$' );
        
        if ( inputs[i].name.search( rE ) == 0 ) {
          if ( inputs[i].checked )
            multiple.push( 
              typeof( inputs[i].value ) != 'undefined' ? 
                inputs[i].value : true 
            );
        }

      }

      if ( multiple.length > 0 )
      return multiple;
      else
        return false;

      break;
    
    case 'inputradio':
    case 'inputradiodynamic':
      
      if ( undefined == form[ field ].length ) {
        value = form[ field ].checked ? form[ field ].value : false;
      }
      else
        for ( i = 0; ( value === false ) && ( i < form[ field ].length ); i++ )
          if ( form[ field ][ i ].checked )
            value = form[ field ][ i ].value;
      
      return value;
      
      break;
    
    case 'select':
    case 'selectfile':
    case 'selectdynamic':
      
      for ( i = 0; ( value === false ) && ( i < form[ field ].length ); i++ )
        if ( form[ field ][ i ].selected ) {
          if ( form[ field ].multiple )
            multiple.push( form[ field ][ i ].value );
          else
            value = form[ field ][ i ].value;
        }
      
      if ( form[ field ].multiple )
        return multiple.length > 0 ? multiple : false;
      else
        return value;
      
      break;
    
    default:
    
      value = form[ field ].value;
      return value;
      
      break;

  }

}

var clonefish_addressChooser_autocomplete;
// ----------------------------------------------------------------------------
function clonefish_setup_addressChooser_widget( elementid, options ) {

  // code below based on examples from
  // http://addresschooser.maptimize.com/

  clonefish_addressChooser_widget = new Maptimize.AddressChooser.Widget({
      onInitialized: onInitialized,
      "country":     options.country,
      "state":       options.state,
      "zip":         options.zip,
      "city":        options.city,
      "street":      options.street,
      "map":         elementid + "map",
      "lat":         elementid + "latfield",
      "lng":         elementid + "lngfield"
  });

  function compileValues() {

    // This function updates the element value
    // based on the value of the lat/long fields (glued together with ###). 
    // This way we the addressChooser element is filled with
    // a single string value, which is still easy to process and validate.
    //
    // As the addressChooser class doesn't have public callback 
    // hook for the marker move event, the function gets called
    // using timeout when JS is enabled. As the server side
    // will repeat the same ### glueing even a missed call to 
    // this function will not break functionality.
    
    // Tiny safeguard for elements not being there.
    if ( !clonefishGetElement( elementid ) || !clonefishGetElement( elementid + 'latfield' ) )
      return;
    
    clonefishGetElement( elementid ).value = 
      clonefishGetElement( elementid + 'latfield' ).value + options.glue +
      clonefishGetElement( elementid + 'lngfield' ).value

    setTimeout( compileValues, 500 );

  }

  compileValues();

  // If you want to customize your map, add code in onInitialized callback
  function onInitialized( clonefish_addressChooser_widget ) {

    // Add default controls
    if ( clonefish_addressChooser_widget.getMap().setUIToDefault ) {
      clonefish_addressChooser_widget.getMap().setUIToDefault();
      clonefish_addressChooser_widget.getMap().setMapType( G_HYBRID_MAP );
    } else // v3 google map api
      clonefish_addressChooser_widget.getMap().setMapTypeId( google.maps.MapTypeId.HYBRID );
    
    clonefish_addressChooser_widget.initMap();
    clonefish_addressChooser_widget.centerOnClientLocation( 6 );
    
    // Observe "suggests:started" to display spinner and disable submit button
    clonefish_addressChooser_widget.addEventListener("suggests:started", function() {
      if ( 
           typeof( 
             clonefishGetElement( 
               clonefish_addressChooser_widget.options.street
             ).addClassName 
           ) == 'function' 
         )
        clonefishGetElement(
          clonefish_addressChooser_widget.options.street
        ).addClassName('spinner');
    });

    // Observe "suggests:found" to hide spinner and enable submit button if a placemark has been found
    clonefish_addressChooser_widget.addEventListener("suggests:found", function(placemarks) {

      if ( 
           typeof( 
             clonefishGetElement( 
               clonefish_addressChooser_widget.options.street
             ).removeClassName 
           ) == 'function' 
         )
        clonefishGetElement(
          clonefish_addressChooser_widget.options.street
        ).removeClassName('spinner');

      clonefishGetElement(
        clonefish_addressChooser_widget.options.street
      ).focus();

      // Reset autocomplete suggestions to new placemarks
      // in case autocomplete is turned on
      if ( typeof( clonefish_addressChooser_autocomplete ) == 'object' ) {

        clonefish_addressChooser_autocomplete.options.array.clear();
        if (placemarks && placemarks.length > 0) {
          for (var i = 0; i < placemarks.length; i++) {
            clonefish_addressChooser_autocomplete.options.array.push(
              clonefish_addressChooser_widget.getAddress(placemarks[i])
            );
          }
          // For autocomplete update
          clonefish_addressChooser_autocomplete.getUpdatedChoices();
          clonefish_addressChooser_autocomplete.show();
        }
        else {
          clonefish_addressChooser_autocomplete.hide();
        }

      }

    });
  }

  if ( options.autofocus && clonefishGetElement( clonefish_addressChooser_widget.options.street ) )
    clonefishGetElement( 
      clonefish_addressChooser_widget.options.street 
    ).focus();

}

// ----------------------------------------------------------------------------
function clonefish_setup_addressChooser_autoComplete( elementid, options ) {

  // code below based on examples from
  // http://addresschooser.maptimize.com/

  // BEGIN AUTOCOMPLETE SETTINGS AND HACKS :)
  // Create a local autocomplete without data.
  // Data will be added dynamically according to map suggestions
  clonefish_addressChooser_autocomplete = 
    new Autocompleter.Local(
      options.street,
      elementid + 'suggests', 
      [], 
      { 
        afterUpdateElement: clonefish_addressChooser_afterUpdateElement, 
        selector:           clonefish_addressChooser_selector 
      }
    );

  // afterUpdateElement callback, display selected item on map
  function clonefish_addressChooser_afterUpdateElement(element, selectedElement) {
    var index = selectedElement.up().immediateDescendants().indexOf(selectedElement);
    clonefish_addressChooser_widget.showPlacemark(index);
    if (
         clonefish_addressChooser_widget.placemarks && 
         ( index < clonefish_addressChooser_widget.length )
       ) {
      
      if ( clonefish_addressChooser_widget.getMap().setUIToDefault )
        clonefish_addressChooser_widget.element.value =
          clonefish_addressChooser_widget.placemarks[ index ].address
      else
        clonefish_addressChooser_widget.element.value =
          clonefish_addressChooser_widget.placemarks[ index ].formatted_address
    }
  }

  // Change selector function
  function clonefish_addressChooser_selector(instance) {
    instance.changed = false;
    return "<ul><li>" + instance.options.array.join("</li><li>") + "</li></ul>";
  }

  // Do not observe keyboard event
  clonefish_addressChooser_autocomplete.onObserverEvent = function() {}

  // Wrap render to update map with selected placemarks
  clonefish_addressChooser_autocomplete.render = 
    clonefish_addressChooser_autocomplete.render.wrap(function(method) {
      method();
      clonefish_addressChooser_widget.showPlacemark(this.index);
    });

  // END AUTOCOMPLETE SETTINGS AND HACKS :)

}

// ----------------------------------------------------------------------------
function clonefish_setup_addressChooser( elementid, options ) {

  // we've got JS: show display addressChooser container, hide fallback DIV
  clonefishGetElement( elementid + 'container' ).style.display = 'block';
  clonefishGetElement( elementid + 'fallbackfields' ).style.display = 'none';
 
  if ( options.autocomplete )
    clonefish_setup_addressChooser_autoComplete( elementid, options );
  
  clonefish_setup_addressChooser_widget( elementid, options );

}

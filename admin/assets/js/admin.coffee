jQuery ( $ ) ->
  $searchBox = $( '#wp-dash-search-box' )
  $results = $( '#wp-dash-results' )
  $searchInput = $( '#wp-dash-search' )
  f = new Fuse(WPDash.posts, { keys: ['title'] })

  $searchInput.on 'blur', ->
    hideSearchBox()

  hideSearchBox = ->
    $searchBox.fadeOut( 200, ->
      $searchInput.val( '' )
      $results.empty()
    )

  showSearchBox = ->
    $searchBox.fadeIn( 200 )
    $searchInput.focus()

  toggleSearchBox = ->
    if $searchBox.is( ':visible' )
      hideSearchBox()
    else
      showSearchBox()

  Mousetrap.bind( ['command+k', 'ctrl+k'], toggleSearchBox )
  Mousetrap.bind( 'esc', -> hideSearchBox() )

  moveSelectedUp = ->
    $selected = $results.find( '.selected' )
    if $selected.prev( '.result' ).length > 0
      $selected.removeClass( 'selected' ).prev( '.result' ).addClass( 'selected' )
  moveSelectedDown = ->
    $selected = $results.find( '.selected' )
    if $selected.next( '.result' ).length > 0
      $selected.removeClass( 'selected' ).next( '.result' ).addClass( 'selected' )

  $searchInput.on 'keyup keydown', ( e ) ->
    if e.keyCode == 38 || e.keyCode == 40
      e.preventDefault()

  $searchInput.on 'keyup', ( e ) ->
    if e.keyCode == 38
      moveSelectedUp()
      return
    if e.keyCode == 40
      moveSelectedDown()
      return
    if e.keyCode == 13
      $selected = $results.find( '.selected a' )
      if $selected.length > 0
        $selected[0].click()
      return

    $results.parent().show()
    $results.empty()
    searchResults = f.search( $( this ).val() )
    count = 1
    for result in searchResults
      link = '/wp-admin/post.php?action=edit&post=' + result.id
      $results.append( '<li class="result"><a href="' + link + '">' + result.title + '</a></li>' )
      count++
      if count == 10
        count = 0
        break

    $results.find( 'li:first' ).addClass( 'selected' )
jQuery ( $ ) ->
  $searchBox = $( '#wp-dash-search-box' )
  $results = $( '#wp-dash-results' )
  $searchInput = $( '#wp-dash-search' )
  f = new Fuse(WPDash.posts, { keys: ['title'] })

  toggleSearchBox = ( e ) ->
    if $searchBox.is( ':visible' )
      $searchBox.fadeOut()
    else
      $searchBox.fadeIn()
      $searchInput.focus()

  Mousetrap.bind(['command+k', 'ctrl+k'], toggleSearchBox )


  $searchInput.on 'keyup', ( e ) ->
    if e.keyCode == 91 || e.keyCode == 17
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
      $results.append( '<li><a href="' + link + '">' + result.title + '</a></li>' )
      count++
      if count == 10
        count = 0
        break

    $results.find( 'li:first' ).addClass( 'selected' )
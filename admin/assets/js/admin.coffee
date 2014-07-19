jQuery ( $ ) ->
  f = new Fuse(WPDash.posts, { keys: ['title'] })
  $results = $( '#wp-dash-results' )

  $( '#wp-dash-search' ).on 'keyup', ( e ) ->
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
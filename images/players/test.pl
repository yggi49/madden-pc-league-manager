#!/usr/bin/perl -w

use strict;
use LWP::Simple;

my ($index, $page, $next, $name, $img, @urls, @players);

for ('A'..'Z') {
    push @urls, 'http://www.nfl.com/players/search?category=lastName&playerType=current&filter='.$_;
}

while (my $url = shift @urls) {
    print "$url\n";
    $index = get($url);

    ($next) = $index =~ m|<a href="([^>"]*)">next</a>|;

    if ($next) {
        $next =~ s/&amp;/&/g;
        $next = 'http://www.nfl.com'.$next;
        unshift @urls, $next;
    }

    @players = $index =~ m|<td><a href="(/players/[^>"]*?)">|g;

    for (@players) {
        $page = get('http://www.nfl.com'.$_);

        ($name) = $page =~ m|<p class="bold">\s*(.*?)\s*</p>|s;
        $name =~ s/\n.*//g;
        $name =~ s/\s*$//g;

        ($img) = $page =~ m|<img src="(http://static\.nfl\.com/static/content/public/image/getty/headshot/.*?)"|;

        print "$name ($img)\n";
        getstore($img, "$name.jpg");
    }
}


use strict;
use File::Basename;
use Encode qw/encode decode/;

my $path = dirname($0) eq '.' ? $ENV{HOMEDRIVE} : dirname($0).'/';

my $content = "";

open FILE, '<:utf8', $path.'temp.php';
while (<FILE>) {
	$content .= $_;
}
close FILE;

open FILE, '>:encoding(gbk)', $path.'temp.php';
print FILE $content;
close FILE;

#!/usr/bin/perl
use strict;

my $psmode;
if ($ARGV[0] eq '-P') {
  $psmode=1; shift;
}
# locate the .puz file
die "USAGE: $0 <file.puz> > <puzzle.html>\n - or -\n$0 -P <file.puz> > <puzzle.html>" unless $ARGV[0];
my $file = $ARGV[0];

# make sure we can read the .puz file
die "file $file does not exist\n" unless -e $file;
die "file $file is not readable. Are you sure it's a .puz file?\n" 
  unless -f _ and -r _;

# read in the .puz file
my $data;
open (my $fh, $file);
{ local $/;
  $data = <$fh>;
}
close $fh;

# get the size of the grid
my ($w, $h) = unpack("CC", substr($data, 0x2C, 2));

# fill in data structures from the file

# First, the answers (which may be scrambled).
my $answerstring = substr($data, 0x34, $w*$h);
my @answergrid; # 2d array
for my $i (0..$h-1){
  # $i = the number of this row
  $answergrid[$i] = [split(//, substr($answerstring, $i*$w, $w))];
}


# Then, the black/white grid pattern.
my $bwstring = substr($data, 0x34+$w*$h, $w*$h);
my @bwgrid; # 2d array to store black vs white squares
for my $i (0..$h-1){
  # $i = the number of this row
  $bwgrid[$i] = [split(//, substr($bwstring, $i*$w, $w))];
}



# Finally, the list of clues not yet numbered.
my $cluestring = substr($data, 0x34+$w*$h+$w*$h); 
# the list of clues contains a few lines of footer material, to be
# dealt with later.
my @newclues = split(/\0/, $cluestring);
# it also contains a few lines at the beginning. The last
# uninteresting line starts with a copyright symbol.
my @header_lines;
for my $clue (@newclues){
  push(@header_lines, shift(@newclues));
  last if index($header_lines[-1], chr(0xA9)) == 0;

}


# Alright! Let's go!
my @numgrid; # 2d array to store clue number positions
# we are going to make sure we are copying the data!
for my $i (0..$#bwgrid){
  for my $j (0..$#{$bwgrid[$i]}){
    $numgrid[$i]->[$j] = $bwgrid[$i]->[$j];
  }
}
my @across; # list of across clues with numbers
my @down; # list of down clues with numbers

# The most pressing issue now is to figure out the numbers and assign
# them to the grid and the clues.

# Black squares will be filled with -1's. We'll have an extra row on
# the top, and column on the left, filled with -1's.

unshift (@bwgrid, [(-1) x $w]);
unshift (@$_, -1) for @bwgrid;

unshift (@numgrid, [(-1) x $w]);
unshift (@$_, -1) for @numgrid;

my $c;
for my $i (1..$h) {
  
  for my $j (1..$w) {
    # this square is $bwgrid[$i]->[$j]

    # add any needed -1's for future iterations
    if ($bwgrid[$i]->[$j] eq '.'){ 
      $bwgrid[$i]->[$j] = -1 ;
      $numgrid[$i]->[$j] = -1;
    }

    next if $bwgrid[$i]->[$j] == -1;


    # if a square has a -1 to its left or top, it's a clue. Let's
    # allocate a number for it.
    if ($bwgrid[$i]->[$j-1] == -1 or $bwgrid[$i-1]->[$j] == -1){
      $c++;
      $numgrid[$i]->[$j] = $c;
    }

    # across clue (-1 to its left)
    if ($bwgrid[$i]->[$j-1] == -1){ 
      push(@across, "$c. ".shift(@newclues)) 
    }

    # down clue (-1 above it)
    if ($bwgrid[$i-1]->[$j] == -1){ 
      push(@down, "$c. ".shift(@newclues)) 
    }
  }
}



if ($psmode) {
  psify_puzzle(\@numgrid, \@across, \@down, \@header_lines, \@newclues);
} else {
  my $header = join "<br />", @header_lines;
  my $footer = join "\n", @newclues;
  htmlify_puzzle(\@numgrid, \@across, \@down, $header, $footer);
}

# subs

sub debug_grid {

  my $twod_array = shift;

  for my $i (0..$#$twod_array){

    print STDERR ">";
    for my $j (0..$#{$twod_array}){
      print STDERR "$twod_array->[$i][$j]  ";
    }

    print STDERR "\n";

  }

  print STDERR "------------------------\n";

}


sub htmlify_puzzle {

  my ($numgrid, $across, $down, $header, $footer) = @_;

  my $html = <<EIEIO;
<html>
<head>
<title>Crossword Puzzle</title>
<style>

//BODY { width: 600px }

TD { background-color: white; width: 25px; height: 25px; margin: 0; padding: 0; vertical-align: top;}

TD.black { background-color: black !important; }

DIV.number { font-size: 8px; font-family: sans-serif; color: #666; margin: 0; padding: 0; }

</style>
</head>
<body>

EIEIO

  # add header elements
  $html .= "$header <br />";


  # here goes the grid!
  $html .= '<table border="1" cellpadding="0" cellspacing="0" align="right">';
  
  for my $i (1..$h){
    
    $html .= '<tr>';
    
    for my $j (1..$w){
      
      if ($numgrid->[$i][$j] == -1){
	# black square
	$html .= '<td class="black"><img src="http://loxosceles.org/black.png" alt=""/></td>'
      } elsif  ($numgrid->[$i][$j] > 0){
	# white square with number
	$html .= qq{<td><div class="number">$numgrid->[$i][$j]</div></td>};
      } else {
	# white square without number
	$html .= '<td>&nbsp;</td>';
      }
      
    }
    
    $html .= "</tr>\n";
    
  }
  
  $html .= '</table>';
  
  # clues
  $html .= '<h3>Across</h3>'.join("<br />\n", @$across);
  $html .= '<h3>Down</h3>'.join("<br />\n", @$down);
  

  # add footer elements
  $html .= "<br /><hr />$footer";

  # now finish
  $html .= '</body></html>';
  print $html;
  
}

sub psify_puzzle {
  my ($numgrid, $across, $down, $header, $footer) = @_;
  my $page_height = 11.0;
  my $page_width = 8.5;
  print <<EOPS;
%!PS-Adobe-2.0
%%PageOrder: Ascend
%%Title: Apr0807.puz
%%Creator: decode_crossword.pl (C) 2007 Beth Skwarecki, Richard M Kreuter
%%BoundingBox: 0 0 612 792
%%DocumentPaperSizes: Letter
%%EndComments
%%BeginProlog

% These are all the various global parameters to this PS program.

% Units of measure.
/cm { 72 2.54 div mul } def
/in { 72 mul } def

% Physical dimensions of the page.
/page-width $page_width in def
/page-height $page_height in def

% Logical dimensions of the puzzle grid
/grid-rows $h def
/grid-cols $w def

% Font and size to use for the header text
/header-font-name /Times-Bold def
/header-font-size 14 def

% Font and size to use for the clue subheader text.  Note: these fonts
% must be ISO-8859-1 encoded.  See the procedure RE below.  Note also:
% Unicode-aware emacsen may transcode iso-8859-1 encoded characters to
% Unicode, which will screw things up.
/clue-header-font-name /ISOTimes-Bold def
/clue-header-font-size 12 def

% Font and size to use for the clue text.
/clue-font-name /ISOTimes-Roman def
/clue-font-size 11 def

% Font and size to use for the labels in the puzzle boxes
/number-font-name /ISOTimes-Roman def
/number-font-size 6 def

% Vertical and horizontal margins around the content in the page.
/page-margin-top .50 in def
/page-margin-bottom .50 in def
/page-margin-left .50 in def
/page-margin-right .50 in def

% Spacing between columns of clues.
/column-space 12 def

% How much of the first page to devote to the puzzle grid.
/grid-share grid-rows 15 le { 1 2 div } { 2 3 div } ifelse def
/numcols grid-rows 15 le { 4 } { 3 } ifelse def

% The rest are procedure definitions and global variables,
% should not need editing

% Re-encode fonts for ISO8859-1.
/RE { % /NewFontName [NewEncodingArray] /FontName RE -
   findfont dup length dict begin
   {
       1 index /FID ne
       {def} {pop pop} ifelse
   } forall
   /Encoding exch def
   /FontName 1 index def
   currentdict definefont pop
   end
} bind def
/ISOTimes-Roman ISOLatin1Encoding /Times-Roman RE
/ISOTimes-Bold ISOLatin1Encoding /Times-Bold RE


/page-visible-horizontal page-width page-margin-left page-margin-right add sub def

/column-width page-visible-horizontal numcols 1 sub column-space mul sub numcols div def

% Total size of the puzzle grid
/grid-size page-visible-horizontal grid-share mul def

/grid-horizontal-position page-width grid-size page-margin-left add sub def
/grid-vertical-position  page-height grid-size page-margin-top add sub def

% Physical size of each puzzle grid cell
/cell-size grid-size grid-cols div def

% Make a box path.
/box {        % w h
  dup         % w h h
  0 exch      % w h 0 h
  rlineto     % w h
  exch        % h w
  dup 0       % h w w 0
  rlineto     % h w
  exch        % w h
  -1 mul      % w -h
  0 exch      % w 0 -h
  rlineto     % w
  -1 mul 0    % -w 0
  rlineto
} def

% Make a square box path.
/square-box { % w
  dup box
} def

% Draw a horizontal row of square boxes.
/draw-grid-row { % rows
  1 sub
  0 1 3 -1 roll {
    gsave
      cell-size mul 0 translate
      newpath 0 0 moveto
      cell-size square-box
      stroke
    grestore
  } for
} def

% Draw a rectangle of rows of boxes.
/draw-grid { % rows cols
  1 sub
  0 1 3 -1 roll {
    gsave
      cell-size mul 0 exch translate
      dup draw-grid-row
    grestore
  } for
  pop
} def

% Move the current path to the bottom left corner of the cell at (row,
% column). Note: row/column is really y/x; inverted to match row-major
% ordering in the host language.
/moveto-cell { % row col
  exch 1 add exch
  cell-size mul exch
  grid-cols exch sub
  cell-size mul
  moveto
} def

% Fill the grid cell at (row, col).
/fill-cell { % row col
  newpath
  moveto-cell
  cell-size square-box
  fill
} def

% Label the grid cell at (row, col) with a string.
/number-cell { % string row col
  newpath
  number-font-name findfont number-font-size scalefont setfont
  moveto-cell
  0 cell-size rmoveto
  2 -1 number-font-size mul rmoveto
  show fill
} def

%% Stuff for filling text in columns.
/first-page true def
/column-number 0 def
/column-start {
    column-number numcols mod dup
    column-width mul exch
    column-space mul add
    page-margin-left add
} def
/column-indent 0 def

/dup2 {    % a b
  dup      % a b b
  3 2 roll % b b a
  dup      % b b a a
  4 1 roll % a b b a
  exch     % a b a b
} def

/nextcolumn-maybe {
    currentpoint exch pop curr-font-size sub page-margin-bottom lt {
	/column-number column-number 1 add def
	column-number numcols eq {
	    showpage
	    /first-page false def
	} if
	column-start page-height page-margin-top sub curr-font-size sub moveto
	first-page {
            currentpoint pop % x
            column-width add grid-horizontal-position ge {
		0 grid-vertical-position column-space sub -1 mul rmoveto
	    } if
	} if
    } if
} def

/nextline {
    currentpoint pop column-start sub -1 mul curr-font-size -1 mul rmoveto
    column-indent 0 rmoveto
} def

/nextline-maybe {   % colwidth text
  dup2              % colwidth text colwidth text
  stringwidth pop   % colwidth text colwidth textwidth
  currentpoint pop  % colwidth text colwidth textwidth hpos
  add               % colwidth text colwidth hoffset
  exch              % colwidth text hoffset colwidth
  column-start add  % colwidth text hoffset colright
  gt {              % colwidth text
    nextline
  } if
  nextcolumn-maybe
} def

/showline {           % colwidth text
    nextcolumn-maybe
    dup length 0 gt { % colwidth text
	( ) search
	{                           % colwidth text2 ( ) text1
            4 -1 roll dup 5 1 roll exch  % colwidth text2 ( ) colwidth text1
	    nextline-maybe          % colwidth text2 ( ) colwidth text1
            show pop                % colwidth text2 ( )
            show                    % colwidth text2
            showline
	}
	{                           % colwidth text
	    nextline-maybe          % colwidth text
	    nextcolumn-maybe
	    show pop                %
	} ifelse
    } if
} def

/show-text-in-column-at { % colwidth text x y
  moveto
  showline
} def

% Show the lines of header.
/show-header-line { % x y text
  clue-header-font-name findfont clue-header-font-size scalefont setfont
  /curr-font-size header-font-size def
  newpath
  3 1 roll moveto
  column-width exch currentpoint show-text-in-column-at
  nextline
  nextline
  currentpoint
  fill
} def

% Show the clue header ("Across" or "Down")
/show-clue-header {
  clue-header-font-name findfont clue-header-font-size scalefont setfont
  /curr-font-size clue-header-font-size def
  newpath
  3 1 roll moveto
  column-width exch currentpoint show-text-in-column-at
  nextline
  currentpoint
  fill
} def

% Show the clue.
/show-clue {
  clue-font-name findfont clue-font-size scalefont setfont
  /curr-font-size clue-font-size def
  dup ( ) search pop
  3 1 roll pop pop
  stringwidth pop ( ) stringwidth pop add /column-indent exch def
  newpath
  3 1 roll moveto
  column-width exch currentpoint show-text-in-column-at
  /column-indent 0 def
  nextline
  currentpoint
  fill
} def
%%EndProlog

EOPS

    print <<EOPS;
gsave
  grid-horizontal-position grid-vertical-position translate
  newpath
  0 0 moveto
  grid-rows grid-cols draw-grid
EOPS
  for my $i (1..$h){
    for my $j (1..$w) {
      if ($numgrid->[$i][$j] == -1) {
	# black square
	printf "  %d %d fill-cell\n", $i-1, $j-1;
      } elsif ($numgrid->[$i][$j] > 0) {
	# white square with number
	printf "  (%d) %d %d number-cell\n", $numgrid->[$i][$j], $i-1, $j-1;
      } else {
	# white square without number, ignore
      }
    }
  }
  print <<EOPS;
grestore

gsave
  newpath
  page-margin-left page-height page-margin-top sub
EOPS

  # FIXME: I dunno whether Ghostscript can handle utf-8 input.  Some
  # kind of string transcoding is probably required for
  for my $line (@$header) {
    $line =~ s/(^\s+|\s+$)//g;
    $line =~ s/([()])/\\\1/g;
    printf "  (%s) show-header-line\n", $line;
  }
  printf "  (Across) show-clue-header\n";
  for my $clue (@$across) {
    printf "  (%s) show-clue\n", $clue;
  }
  printf "  (Down) show-clue-header\n";
  for my $clue (@$down) {
    printf "  (%s) show-clue\n", $clue;
  }

  print <<EOPS;
  pop pop
  fill
grestore
showpage
EOPS
}

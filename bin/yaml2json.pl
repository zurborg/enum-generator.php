#!/usr/bin/perl

use strictures 2;
use autodie;
use File::Slurp qw(read_file write_file);
use YAML;
use JSON;
use JSON::PP::Boolean;

my ($infile, $outfile) = @ARGV;

$infile ||= \*STDIN;
$outfile ||= \*STDOUT;

my $yaml = read_file($infile);

my $boolean_header = <<'EOF';
___t: &true  !!perl/scalar:JSON::PP::Boolean 1
___f: &false !!perl/scalar:JSON::PP::Boolean 0
EOF

$yaml =~ s{^\s*(---)?\s*}{---\n$boolean_header\n}s;

$yaml = YAML::Load($yaml);

delete @$yaml{qw{___t ___f}};

my $json = JSON->new->utf8->pretty->canonical->allow_blessed->encode($yaml);

write_file($outfile, $json);

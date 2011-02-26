#!/usr/bin/env ruby
# Automatically add/remove files form an SVN working copy

result = `svn status`
lines = result.split "\n"

splitlines = []

lines.each do |l|
    s = l.split " "

    if s[0] =~ /\?/ 
        s.shift
        `svn add "#{s.join(' ')}"`
    elsif s[0] =~ /\!/ 
        s.shift
        `svn rm "#{s.join(' ')}"`
    end
end

<?xml version="1.0" encoding="UTF-8"?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/"
  xmlns:moz="http://www.mozilla.org/2006/browser/search/">
  <ShortName>$Title</ShortName>
  <Description>$Title</Description>
  <InputEncoding>$Encoding</InputEncoding>
  <% if IcoIcon %><Image width="32" height="32" type="image/x-icon">$IcoIcon</Image><% end_if %>
  <% if SearchURL %><Url type="text/html" template="{$SearchURL}"/><% end_if %>
  <% if SuggestionURL %><Url type="application/x-suggestions+json" template="$SuggestionURL"/><% end_if %>
  <% if SearchForm %><moz:SearchForm>$SearchForm</moz:SearchForm><% end_if %>
</OpenSearchDescription>

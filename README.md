# XML Feed Parser for the first repetitive item
## About
Appjobs technical assessment for refactoring and improving a Laravel app's codebase

## Requirements 
- In order to build the working environment, `docker` & `docker-compose` must be installed on your end

## Environment installation
Run `make start` in the root folder. 

If `make` cannot be run for various reasons, please run manually the following commands: 
- `docker-compose up -d`
- `docker exec -u appjobsuser:appjobsgroup appjobs-php composer install`

# Technical assessment description
Build a feature that will automatically show the first repetitive node from an XML feed.
The goal is to read through heavy and big XML files.
Implement a REST API endpoint that will GET the URL of an XML file and will return, in an JSON-format response, the first repetitive
node and its sub-nodes with stripped <![CDATA]> tag from the content.

## Current solution approach
From the beginning, we should expect some large size of the XMLs. This means that we shouldn't parse the entire XML to discover the first repetitive node.
For that reason we should load the XML as a stream, byte by byte and detect what type of tag we encounter.
The algorithm runs until it finds a starting tag already existent on the same depth.

The flow could be summarized as it follows:
1. Load the XML from the given url
2. Read 1 byte at a time and detect if we're hitting a starting tag or a closing tag storing the current tag in a buffer
3. Check if we've hit a starting tag, if it already exists on the current level(depth). If so, then stop reading from file and proceed with **6.**
4. If not, append the buffer data to the new filtered XML. Some preconditions are applied in order to prevent the addition of meta tags.
5. Increase or decrease the level(depth) based on the tag type, and keep track of the current tag. From here start again from **2.**
6. Once we stopped reading from the file some escape rules are applied
7. Based of the level at which we stopped reading the XML, we close the open tags if it's necessary
8. Convert the filtered XML to Json and return it as an api response.

### Important files
- `App\Http\Services\ParserService` - Service responsible for XML parsing and node isolation;
- `App\Http\Requests\XmlParseRequest` - Request validator for api endpoint;
- `App\Dto\XmlStateDto` - Dto responsible with storing the filtered XML state;

### How to run it
http://localhost/api/v1/xmlToJson?url=https://appjobs-general.s3.eu-west-1.amazonaws.com/test-xml-feeds/feed_3.xml

# Your improved solution
Although the current solution is providing the expected results, there are several weak points that should to be improved:
- XML parsing is looking out-dated, over-complicated and hard to maintain or review
- Finding the first repetitive node could be achieved in a recursive way
- Poor handle / display of errors
- Code tests are missing

Your task is to provide a better, re-factored solution that will at least improve the above pain points.

## Key highlights not to miss:
1. Don't re-invent the wheel!
2. You will cover the given functionality with all necessary tests.
3. Please pay as much attention on performance too.
4. In the end, we expect for a simple and functional endpoint so no need for over-complicating stuff, 
like implementing user authentication or roles.

## Final notes
- Please share your solution via GIT and explain how to run it
- In order to run the tests, you can use `make test`
- The XML URLs for tests:
  - https://appjobs-general.s3.eu-west-1.amazonaws.com/test-xml-feeds/feed_1.xml
  - https://appjobs-general.s3.eu-west-1.amazonaws.com/test-xml-feeds/feed_2.xml
  - https://appjobs-general.s3.eu-west-1.amazonaws.com/test-xml-feeds/feed_3.xml
  - https://appjobs-general.s3.eu-west-1.amazonaws.com/test-xml-feeds/feed_4.xml
  - https://appjobs-general.s3.eu-west-1.amazonaws.com/test-xml-feeds/feed_5.xml
  - https://appjobs-general.s3.eu-west-1.amazonaws.com/test-xml-feeds/feed_6.xml

package com.totsp.crossword.io;

import java.io.DataOutputStream;
import java.io.InputStream;
import java.io.UnsupportedEncodingException;
import java.net.URLDecoder;
import java.util.Date;
import java.util.HashMap;
import java.util.Map;

import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.xml.sax.Attributes;
import org.xml.sax.InputSource;
import org.xml.sax.SAXException;
import org.xml.sax.XMLReader;
import org.xml.sax.helpers.DefaultHandler;

import com.totsp.crossword.puz.Box;
import com.totsp.crossword.puz.Puzzle;

/**
 * Converts a puzzle from the XML format used by JPZ puzzles into the Across
 * Lite .puz format.  Strings are HTML formatted, UTF-8.  Any unsupported features
 * are either ignored or cause abort.  The format is:
 *
 * <crossword-compiler-applet>
 *   ...
 *   <rectangular-puzzle xmlns="http://crossword.info/xml/rectangular-puzzle"
 *       alphabet="ABCDEFGHIJKLMNOPQRSTUVWXYZ">
 *     <metadata>
 *       <title>[Title]</title>
 *       <creator>[Author]</creator>
 *       <copyright>[Copyright]</copyright>
 *       <description>[Notes]</description>
 *     </metadata>
 *     <crossword>
 *       <grid width="[Width]" height="[Height]">
 *         <grid-look numbering-scheme="normal" ... />
 *         <cell x="1" y="1" solution="M" number="1"></cell>
 *         ...
 *         <cell x="1" y="6" type="block"</cell>
 *         ...
 *       </grid>
 *       ...
 *       <clues ordering="normal">
 *         <title>...Across...</title>
 *         <clue ... number="1">...</clue>
 *         ...
 *       </clues>
 *       <clues ordering="normal">
 *         <title>...Down...</title>
 *         <clue ... number="1">...</clue>
 *         ...
 *       </clues>
 *     </crossword>
 *   </rectangular-puzzle>
 * </crossword-compiler-applet>
 */
public class JPZIO {
    private static String CHARSET_NAME = "utf8";

    private static class JPZXMLParser extends DefaultHandler {
        private Puzzle puz;
        private Map<Integer, String> acrossNumToClueMap = new HashMap<Integer, String>();
        private Map<Integer, String> downNumToClueMap = new HashMap<Integer, String>();
        private int[][] clueNums;

        private boolean inClues = false;
        private boolean inClueTitle = false;
        private boolean inClue = false;
        private int clueNumber = 0;
        private boolean inAcross = false;
        private boolean inDown = false;
        private boolean inMetadata = false;
        private boolean inTitle = false;
        private boolean inAuthor = false;
        private boolean inCopyright = false;
        private boolean inDescription = false;

        private Box[][] boxes;
        private int width;
        private int height;

        private int maxClueNum = -1;

        private StringBuilder curBuffer;

        public JPZXMLParser(Puzzle puz) {
            this.puz = puz;
        }

        @Override
        public void startElement(String nsURI, String strippedName,
                String tagName, Attributes attributes) throws SAXException {
            strippedName = strippedName.trim();
            String name = strippedName.length() == 0 ? tagName.trim() : strippedName;

            if (name.equalsIgnoreCase("metadata")) {
                inMetadata = true;
            } else if (inMetadata) {
                if (name.equalsIgnoreCase("title")) {
                    inTitle = true;
                    curBuffer = new StringBuilder();
                } else if (name.equalsIgnoreCase("creator")) {
                    inAuthor = true;
                    curBuffer = new StringBuilder();
                } else if (name.equalsIgnoreCase("copyright")) {
                    inCopyright = true;
                    curBuffer = new StringBuilder();
                } else if (name.equalsIgnoreCase("description")) {
                    inDescription = true;
                    curBuffer = new StringBuilder();
                }
            } else if (name.equalsIgnoreCase("grid")) {
                width = Integer.parseInt(attributes.getValue("width"));
                height = Integer.parseInt(attributes.getValue("height"));
                puz.setWidth(width);
                puz.setHeight(height);
                boxes = new Box[height][width];
                clueNums = new int[height][width];
            } else if (name.equalsIgnoreCase("cell")) {
                int x = Integer.parseInt(attributes.getValue("x")) - 1;
                int y = Integer.parseInt(attributes.getValue("y")) - 1;
                String sol = attributes.getValue("solution");
                if (sol != null) {
                    boxes[y][x] = new Box();
                    boxes[y][x].setSolution(sol.charAt(0));
                    if ("circle".equalsIgnoreCase(attributes.getValue("background-shape"))) {
                        puz.setGEXT(true);
                        boxes[y][x].setCircled(true);
                    }
                    String number = attributes.getValue("number");
                    if (number != null) {
                        clueNums[y][x] = Integer.parseInt(number);
                    }
                }
            } else if (name.equalsIgnoreCase("clues")) {
                inClues = true;
            } else if (inClues) {
                if (name.equalsIgnoreCase("title")) {
                    inClueTitle = true;
                    curBuffer = new StringBuilder();
                } else if (name.equalsIgnoreCase("clue")) {
                    inClue = true;
                    clueNumber = Integer.parseInt(attributes.getValue("number"));
                    if (clueNumber > maxClueNum) {
                        maxClueNum = clueNumber;
                    }
                    curBuffer = new StringBuilder();
                }
            }
        }

        @Override
        public void characters(char[] ch, int start, int length) {
            if (curBuffer != null) {
                curBuffer.append(ch, start, length);
            }
        }

        @Override
        public void endElement(String nsURI, String strippedName,
                String tagName) throws SAXException {
            strippedName = strippedName.trim();
            String name = strippedName.length() == 0 ? tagName.trim() : strippedName;

            if (name.equalsIgnoreCase("metadata")) {
                inMetadata = false;
            } else if (inMetadata) {
                if (name.equalsIgnoreCase("title")) {
                    puz.setTitle(curBuffer.toString());
                    inTitle = false;
                    curBuffer = null;
                } else if (name.equalsIgnoreCase("creator")) {
                    puz.setAuthor(curBuffer.toString());
                    inAuthor = false;
                    curBuffer = null;
                } else if (name.equalsIgnoreCase("copyright")) {
                    puz.setCopyright(curBuffer.toString());
                    inCopyright = false;
                    curBuffer = null;
                } else if (name.equalsIgnoreCase("description")) {
                    puz.setNotes(curBuffer.toString());
                    inDescription = false;
                    curBuffer = null;
                }
            } else if (name.equalsIgnoreCase("grid")) {
                puz.setBoxes(boxes);
            } else if (name.equalsIgnoreCase("clues")) {
                inClues = false;
                inAcross = false;
                inDown = false;
            } else if (inClues) {
                if (name.equalsIgnoreCase("title")) {
                    String title = curBuffer.toString();
                    if (title.contains("Across")) {
                        inAcross = true;
                    } else if (title.contains("Down")) {
                        inDown = true;
                    } else {
                        throw new SAXException("Clue list is neither across nor down.");
                    }
                    inClueTitle = false;
                    curBuffer = null;
                } else if (name.equalsIgnoreCase("clue")) {
                    if (inAcross) {
                        try {
                            acrossNumToClueMap.put(clueNumber, URLDecoder.decode(curBuffer.toString(), "utf8"));
                        } catch (UnsupportedEncodingException e) {
                            acrossNumToClueMap.put(clueNumber, curBuffer.toString());
                        }
                    } else if (inDown) {
                        try {
                            downNumToClueMap.put(clueNumber, URLDecoder.decode(curBuffer.toString(), "utf8"));
                        } catch (UnsupportedEncodingException e) {
                            downNumToClueMap.put(clueNumber, curBuffer.toString());
                        }
                    } else {
                        throw new SAXException("Unexpected end of clue tag.");
                    }
                }
            } else if (name.equalsIgnoreCase("crossword")) {
                int numberOfClues = acrossNumToClueMap.size() + downNumToClueMap.size();
                puz.setNumberOfClues(numberOfClues);
                String[] rawClues = new String[numberOfClues];
                int i = 0;
                for(int clueNum = 1; clueNum <= maxClueNum; clueNum++) {
                    if(acrossNumToClueMap.containsKey(clueNum)) {
                        rawClues[i] = acrossNumToClueMap.get(clueNum);
                        i++;
                    }
                    if(downNumToClueMap.containsKey(clueNum)) {
                        rawClues[i] = downNumToClueMap.get(clueNum);
                        i++;
                    }
                }
                puz.setRawClues(rawClues);

                // verify clue numbers
                for (int y = 0; y < height; y++) {
                    for (int x = 0; x < width; x++) {
                        if (clueNums[y][x] != 0) {
                            if (puz.getBoxes()[y][x].getClueNumber() != clueNums[y][x]) {
                                throw new SAXException("Irregular numbering scheme.");
                            }
                        }
                    }
                }
            }
        }
    }

    public static boolean convertJPZPuzzle(InputStream is, DataOutputStream os,
            Date d) {
        Puzzle puz = new Puzzle();
        puz.setDate(d);
        SAXParserFactory factory = SAXParserFactory.newInstance();
        try {
            SAXParser parser = factory.newSAXParser();
            //parser.setProperty("http://xml.org/sax/features/validation", false);
            XMLReader xr = parser.getXMLReader();
            xr.setContentHandler(new JPZXMLParser(puz));
            xr.parse(new InputSource(is));

            puz.setVersion(IO.VERSION_STRING);

            IO.saveNative(puz, os);
            return true;
        } catch (Exception e) {
            e.printStackTrace();
            System.err.println("Unable to parse XML file: " + e.getMessage());
            return false;
        }
    }
}

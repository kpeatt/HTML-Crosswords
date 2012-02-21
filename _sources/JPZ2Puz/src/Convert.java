import java.io.BufferedOutputStream;
import java.io.DataOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.InputStream;
import java.io.OutputStreamWriter;
import java.util.Date;
import java.util.Scanner;
import java.util.zip.ZipEntry;
import java.util.zip.ZipInputStream;

import com.totsp.crossword.io.JPZIO;


public class Convert {
    public static boolean convertFile(File jpz)  throws Exception {
        InputStream is = new FileInputStream(jpz);
        File tmpFile = null;

        // try reading as .zip file - if it fails, assume already decompressed
        try {
            ZipInputStream zis = new ZipInputStream(is);
            ZipEntry entry = zis.getNextEntry();
            String entryName = entry.getName();
            tmpFile = new File(entryName + ".tmp");
            BufferedOutputStream dest = new BufferedOutputStream(new FileOutputStream(tmpFile), 2048);
            int count;
            byte[] data = new byte[2048];
            while ((count = zis.read(data, 0, 2048)) != -1) {
                dest.write(data, 0, count);
            }
            dest.flush();
            dest.close();
            zis.close();

            is = new FileInputStream(tmpFile);
        } catch (Exception e) {
            is.close();
            is = new FileInputStream(jpz);
        }

        // Replace invalid characters with safe equivalents
        File replFile = new File(jpz.getAbsoluteFile() + ".repl");
        Scanner in = new Scanner(is, "utf-8");
        OutputStreamWriter out = new OutputStreamWriter(new FileOutputStream(replFile), "utf-8");
        while (in.hasNextLine()) {
            String line = in.nextLine();
            line = line.replaceAll("&nbsp;", " ");
            line = line.replaceAll("%", "%25");
            line = line.replaceAll("\\+", "%2B");
            line = line.replaceAll("Ò", "\"");
            line = line.replaceAll("Ó", "\"");
            out.write(line + "\n");
        }
        out.flush();
        out.close();
        is.close();

        // Fix charset
        File charFile = new File(jpz.getAbsolutePath() + ".char");
        is = new FileInputStream(replFile);
        in = new Scanner(is, "iso8859-1");
        out = new OutputStreamWriter(new FileOutputStream(charFile), "utf-8");
        while (in.hasNextLine()) {
            out.write(in.nextLine() + "\n");
        }
        out.flush();
        out.close();
        is.close();

        is = new FileInputStream(charFile);
        File output = new File(jpz.getAbsolutePath().replace(".jpz", ".puz"));
        DataOutputStream os = new DataOutputStream(new FileOutputStream(output));
        boolean retVal = JPZIO.convertJPZPuzzle(is, os, new Date());
        os.close();
        if (!retVal) {
            System.err.println("Unable to convert puzzle.");
            output.delete();
        } else {
            System.out.println("Puzzle converted successfully.");
        }

        is.close();

        replFile.delete();
        charFile.delete();
        if (tmpFile != null) {
            tmpFile.delete();
        }
        return retVal;
    }
}

import java.awt.BorderLayout;
import java.awt.EventQueue;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.io.File;
import java.util.prefs.Preferences;

import javax.swing.JButton;
import javax.swing.JFileChooser;
import javax.swing.JFrame;
import javax.swing.JOptionPane;


public class ConvertUI {

    private JFrame frame;
    private JFileChooser fc = new JFileChooser();
    private Preferences prefs = Preferences.userNodeForPackage(ConvertUI.class);

    /**
     * Launch the application.
     */
    public static void main(String[] args) throws Exception {
        if (args.length == 1) {
            File jpz = new File(args[0]);
            Convert.convertFile(jpz);
        } else {
            EventQueue.invokeLater(new Runnable() {
                public void run() {
                    try {
                        ConvertUI window = new ConvertUI();
                        window.frame.setVisible(true);
                    } catch (Exception e) {
                        e.printStackTrace();
                    }
                }
            });
        }
    }

    /**
     * Create the application.
     */
    public ConvertUI() {
        initialize();
    }

    /**
     * Initialize the contents of the frame.
     */
    private void initialize() {
        frame = new JFrame();
        frame.setBounds(100, 100, 218, 64);
        frame.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);

        JButton btnSelectjpzTo = new JButton("Select .jpz to convert...");
        btnSelectjpzTo.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent arg0) {
                File loc = null;
                try {
                    loc = new File(prefs.get("jpz_dir", null));
                } catch (NullPointerException e) {

                }
                fc.setCurrentDirectory(loc);
                int retVal = fc.showOpenDialog(frame);
                if (retVal == JFileChooser.APPROVE_OPTION) {
                    File f = fc.getSelectedFile();
                    prefs.put("jpz_dir", f.getParent());
                    String msg = "Converted successfully!";
                    try {
                        if (!Convert.convertFile(f)) {
                            msg = "Unable to convert.";
                        }
                    } catch (Exception e) {
                       msg = "Unable to convert: " + e.getMessage();
                    }
                    JOptionPane.showMessageDialog(frame, msg);
                }
            }
        });
        frame.getContentPane().add(btnSelectjpzTo, BorderLayout.CENTER);
    }

}

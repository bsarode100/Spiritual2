package com.spiritualshaadi;

import android.Manifest;
import android.annotation.SuppressLint;
import android.content.ActivityNotFoundException;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.graphics.Bitmap;
import android.graphics.Color;
import android.graphics.Rect;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.provider.MediaStore;
import android.view.View;
import android.view.ViewGroup;
import android.view.ViewTreeObserver;
import android.webkit.CookieManager;
import android.webkit.ValueCallback;
import android.webkit.WebChromeClient;
import android.webkit.WebResourceError;
import android.webkit.WebResourceRequest;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import androidx.core.content.FileProvider;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowCompat;
import androidx.core.view.WindowInsetsCompat;
import androidx.core.view.WindowInsetsControllerCompat;

import java.io.File;
import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;

public class MainActivity extends AppCompatActivity {

    private static final String APP_URL = "https://spiritualshaadi.com";
    private static final int FILE_CHOOSER_REQUEST_CODE = 1001;
    private static final int PERMISSION_REQUEST_CODE = 1002;

    private WebView webView;
    private ProgressBar progressBar;
    private LinearLayout offlineLayout;
    private Button retryButton;

    private ValueCallback<Uri[]> filePathCallback;
    private String cameraPhotoPath;
    private long backPressedTime = 0;

    @SuppressLint("SetJavaScriptEnabled")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        // Enable edge-to-edge window drawing
        WindowCompat.setDecorFitsSystemWindows(getWindow(), false);

        setContentView(R.layout.activity_main);

        // Configure system bar icons (dark icons for light cream background)
        WindowInsetsControllerCompat insetsController = WindowCompat.getInsetsController(getWindow(), getWindow().getDecorView());
        if (insetsController != null) {
            insetsController.setAppearanceLightStatusBars(true);
            insetsController.setAppearanceLightNavigationBars(true);
        }
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
            getWindow().setStatusBarColor(Color.TRANSPARENT);
            getWindow().setNavigationBarColor(Color.TRANSPARENT);
        }

        View rootLayout = findViewById(R.id.rootLayout);
        View statusBarSpacer = findViewById(R.id.statusBarSpacer);

        // Dynamic insets: top status bar spacer + soft keyboard (IME) responsive resize
        setupWindowInsetsAndKeyboard(rootLayout, statusBarSpacer);

        webView = findViewById(R.id.webView);
        progressBar = findViewById(R.id.progressBar);
        offlineLayout = findViewById(R.id.offlineLayout);
        retryButton = findViewById(R.id.retryButton);

        setupWebView();
        setupOfflineRetry();

        if (savedInstanceState == null) {
            webView.loadUrl(APP_URL);
        } else {
            webView.restoreState(savedInstanceState);
        }

        checkPermissions();
    }

    /**
     * Handles status bar height dynamically and ensures the WebView resizes smoothly
     * when the soft keyboard (IME) appears, keeping inputs visible and eliminating
     * awkward blank spaces below bottom navigation tabs.
     */
    private void setupWindowInsetsAndKeyboard(View rootLayout, View statusBarSpacer) {
        if (rootLayout == null) return;

        // 1. AndroidX WindowInsets listener for status bar cutout & soft keyboard (IME)
        ViewCompat.setOnApplyWindowInsetsListener(rootLayout, (v, windowInsets) -> {
            Insets statusBars = windowInsets.getInsets(
                    WindowInsetsCompat.Type.statusBars() | WindowInsetsCompat.Type.displayCutout()
            );
            Insets imeInsets = windowInsets.getInsets(WindowInsetsCompat.Type.ime());

            if (statusBarSpacer != null) {
                ViewGroup.LayoutParams lp = statusBarSpacer.getLayoutParams();
                if (lp.height != statusBars.top) {
                    lp.height = statusBars.top;
                    statusBarSpacer.setLayoutParams(lp);
                }
            }

            // Keyboard responsive adjustment:
            // When soft keyboard (IME) opens, pad bottom to shrink WebView above the keyboard.
            // When keyboard is closed, pad bottom to 0 for a seamless edge-to-edge full screen with no tab gaps.
            int imeBottom = imeInsets.bottom;
            applyBottomPadding(rootLayout, imeBottom);

            return windowInsets;
        });

        // 2. ViewTreeObserver fallback: ensures keyboard resize works across all Android OEM skins (OneUI, MIUI, ColorOS)
        rootLayout.getViewTreeObserver().addOnGlobalLayoutListener(new ViewTreeObserver.OnGlobalLayoutListener() {
            private int lastReportedPadding = -1;

            @Override
            public void onGlobalLayout() {
                Rect r = new Rect();
                rootLayout.getWindowVisibleDisplayFrame(r);
                int screenHeight = rootLayout.getRootView().getHeight();
                int keypadHeight = screenHeight - r.bottom;

                // Threshold of 15% screen height distinguishes the soft keyboard from transient UI bar shifts
                int targetPadding = 0;
                if (keypadHeight > screenHeight * 0.15) {
                    targetPadding = keypadHeight;
                }

                if (targetPadding != lastReportedPadding) {
                    lastReportedPadding = targetPadding;
                    applyBottomPadding(rootLayout, targetPadding);
                }
            }
        });
    }

    private void applyBottomPadding(View rootLayout, int bottomPadding) {
        if (rootLayout == null) return;
        if (rootLayout.getPaddingBottom() != bottomPadding) {
            rootLayout.setPadding(0, 0, 0, bottomPadding);
            if (webView != null) {
                webView.requestLayout();
            }
        }
    }

    @SuppressLint("SetJavaScriptEnabled")
    private void setupWebView() {
        WebSettings settings = webView.getSettings();
        settings.setJavaScriptEnabled(true);
        settings.setDomStorageEnabled(true);
        settings.setDatabaseEnabled(true);
        settings.setLoadWithOverviewMode(true);
        settings.setUseWideViewPort(true);
        settings.setSupportZoom(false);
        settings.setBuiltInZoomControls(false);
        settings.setDisplayZoomControls(false);
        settings.setTextZoom(100);
        webView.setOverScrollMode(View.OVER_SCROLL_NEVER);
        webView.setHorizontalScrollBarEnabled(false);
        settings.setAllowFileAccess(true);
        settings.setAllowContentAccess(true);
        settings.setMediaPlaybackRequiresUserGesture(false);

        // Identify native app in User-Agent so website can suppress app install prompts and optimize header padding
        String defaultUa = settings.getUserAgentString();
        settings.setUserAgentString((defaultUa != null ? defaultUa : "") + " SpiritualShaadiApp/1.0.5");

        webView.addJavascriptInterface(new Object() {
            @android.webkit.JavascriptInterface
            public boolean isNative() {
                return true;
            }
        }, "SpiritualShaadiNative");

        // Enable Cookies
        CookieManager cookieManager = CookieManager.getInstance();
        cookieManager.setAcceptCookie(true);
        cookieManager.setAcceptThirdPartyCookies(webView, true);

        // WebChromeClient for File Uploads and Progress
        webView.setWebChromeClient(new WebChromeClient() {
            @Override
            public void onProgressChanged(WebView view, int newProgress) {
                if (newProgress < 100) {
                    progressBar.setVisibility(View.VISIBLE);
                    progressBar.setProgress(newProgress);
                } else {
                    progressBar.setVisibility(View.GONE);
                }
            }

            @Override
            public boolean onShowFileChooser(WebView webView, ValueCallback<Uri[]> filePathCallback, FileChooserParams fileChooserParams) {
                if (MainActivity.this.filePathCallback != null) {
                    MainActivity.this.filePathCallback.onReceiveValue(null);
                }
                MainActivity.this.filePathCallback = filePathCallback;

                Intent takePictureIntent = null;
                File photoFile = null;
                try {
                    photoFile = createImageFile();
                    cameraPhotoPath = photoFile.getAbsolutePath();
                } catch (IOException ex) {
                    // Could not create photo file
                }

                if (photoFile != null) {
                    Uri photoURI = FileProvider.getUriForFile(
                            MainActivity.this,
                            getApplicationContext().getPackageName() + ".fileprovider",
                            photoFile
                    );
                    takePictureIntent = new Intent(MediaStore.ACTION_IMAGE_CAPTURE);
                    takePictureIntent.putExtra(MediaStore.EXTRA_OUTPUT, photoURI);
                    takePictureIntent.addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION | Intent.FLAG_GRANT_WRITE_URI_PERMISSION);
                }

                Intent contentSelectionIntent = new Intent(Intent.ACTION_GET_CONTENT);
                contentSelectionIntent.addCategory(Intent.CATEGORY_OPENABLE);
                contentSelectionIntent.setType("image/*");

                Intent[] intentArray;
                if (takePictureIntent != null) {
                    intentArray = new Intent[]{takePictureIntent};
                } else {
                    intentArray = new Intent[0];
                }

                Intent chooserIntent = new Intent(Intent.ACTION_CHOOSER);
                chooserIntent.putExtra(Intent.EXTRA_INTENT, contentSelectionIntent);
                chooserIntent.putExtra(Intent.EXTRA_TITLE, "Upload Profile Photo");
                chooserIntent.putExtra(Intent.EXTRA_INITIAL_INTENTS, intentArray);

                try {
                    startActivityForResult(chooserIntent, FILE_CHOOSER_REQUEST_CODE);
                } catch (ActivityNotFoundException e) {
                    MainActivity.this.filePathCallback = null;
                    Toast.makeText(MainActivity.this, "Cannot open file picker", Toast.LENGTH_SHORT).show();
                    return false;
                }
                return true;
            }
        });

        // WebViewClient for Navigation and Link Interception
        webView.setWebViewClient(new WebViewClient() {
            @Override
            public boolean shouldOverrideUrlLoading(WebView view, WebResourceRequest request) {
                String url = request.getUrl().toString();
                return handleUrl(url);
            }

            @Override
            public boolean shouldOverrideUrlLoading(WebView view, String url) {
                return handleUrl(url);
            }

            private boolean handleUrl(String url) {
                // Internal spiritualshaadi URLs
                if (url.startsWith("https://spiritualshaadi.com") || url.startsWith("http://spiritualshaadi.com")) {
                    return false; // Load in WebView
                }

                // UPI Payment Apps (Google Pay, PhonePe, Paytm, BHIM, etc.)
                if (url.startsWith("upi://")) {
                    try {
                        Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
                        intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                        Intent chooser = Intent.createChooser(intent, "Pay with UPI App");
                        chooser.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                        startActivity(chooser);
                    } catch (ActivityNotFoundException e) {
                        Toast.makeText(MainActivity.this, "No UPI payment app found on your device", Toast.LENGTH_LONG).show();
                    }
                    return true;
                }

                // Android Intent schemes (e.g. intent://pay?...#Intent;scheme=upi;package=...;end)
                if (url.startsWith("intent://")) {
                    try {
                        Intent intent = Intent.parseUri(url, Intent.URI_INTENT_SCHEME);
                        if (intent != null) {
                            String pkg = intent.getPackage();
                            Intent launchIntent = new Intent(Intent.ACTION_VIEW);
                            if (intent.getData() != null) {
                                launchIntent.setData(intent.getData());
                            }
                            if (pkg != null && !pkg.isEmpty()) {
                                launchIntent.setPackage(pkg);
                            }
                            launchIntent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);

                            try {
                                startActivity(launchIntent);
                                return true;
                            } catch (ActivityNotFoundException e) {
                                String appName = "Requested UPI app";
                                if ("com.google.android.apps.nbu.paisa.user".equals(pkg)) {
                                    appName = "Google Pay";
                                } else if ("com.phonepe.app".equals(pkg)) {
                                    appName = "PhonePe";
                                } else if ("net.one97.paytm".equals(pkg)) {
                                    appName = "Paytm";
                                } else if ("com.dreamplug.androidapp".equals(pkg)) {
                                    appName = "CRED";
                                }
                                Toast.makeText(MainActivity.this, appName + " is not installed on your phone. Please choose another UPI app or scan QR.", Toast.LENGTH_LONG).show();
                                return true;
                            }
                        }
                    } catch (Exception e) {
                        Toast.makeText(MainActivity.this, "Unable to launch payment app", Toast.LENGTH_SHORT).show();
                    }
                    return true;
                }

                // WhatsApp, Phone, Email
                if (url.startsWith("whatsapp://") || url.startsWith("tel:") || url.startsWith("mailto:")) {
                    try {
                        Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
                        startActivity(intent);
                    } catch (ActivityNotFoundException e) {
                        Toast.makeText(MainActivity.this, "Application not found", Toast.LENGTH_SHORT).show();
                    }
                    return true;
                }

                // Any other external links -> open in default browser
                try {
                    Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
                    startActivity(intent);
                } catch (Exception ignored) {}
                return true;
            }

            @Override
            public void onPageStarted(WebView view, String url, Bitmap favicon) {
                offlineLayout.setVisibility(View.GONE);
                webView.setVisibility(View.VISIBLE);
            }

            @Override
            public void onPageFinished(WebView view, String url) {
            }

            @Override
            public void onReceivedError(WebView view, WebResourceRequest request, WebResourceError error) {
                if (request.isForMainFrame()) {
                    webView.setVisibility(View.GONE);
                    offlineLayout.setVisibility(View.VISIBLE);
                }
            }
        });
    }

    private void setupOfflineRetry() {
        retryButton.setOnClickListener(v -> {
            offlineLayout.setVisibility(View.GONE);
            webView.setVisibility(View.VISIBLE);
            webView.reload();
        });
    }

    private File createImageFile() throws IOException {
        String timeStamp = new SimpleDateFormat("yyyyMMdd_HHmmss", Locale.getDefault()).format(new Date());
        String imageFileName = "JPEG_" + timeStamp + "_";
        File storageDir = getExternalFilesDir(Environment.DIRECTORY_PICTURES);
        return File.createTempFile(imageFileName, ".jpg", storageDir);
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, @Nullable Intent data) {
        if (requestCode == FILE_CHOOSER_REQUEST_CODE) {
            if (filePathCallback == null) {
                super.onActivityResult(requestCode, resultCode, data);
                return;
            }

            Uri[] results = null;
            if (resultCode == RESULT_OK) {
                if (data == null || data.getData() == null) {
                    // If no data, it was a camera capture
                    if (cameraPhotoPath != null) {
                        File file = new File(cameraPhotoPath);
                        if (file.exists() && file.length() > 0) {
                            results = new Uri[]{Uri.fromFile(file)};
                        }
                    }
                } else {
                    String dataString = data.getDataString();
                    if (dataString != null) {
                        results = new Uri[]{Uri.parse(dataString)};
                    }
                }
            }

            filePathCallback.onReceiveValue(results);
            filePathCallback = null;
            return;
        }

        super.onActivityResult(requestCode, resultCode, data);
    }

    @Override
    public void onBackPressed() {
        if (webView.canGoBack()) {
            webView.goBack();
        } else {
            if (backPressedTime + 2000 > System.currentTimeMillis()) {
                super.onBackPressed();
            } else {
                Toast.makeText(this, "Press back again to exit SpiritualShaadi", Toast.LENGTH_SHORT).show();
                backPressedTime = System.currentTimeMillis();
            }
        }
    }

    @Override
    protected void onSaveInstanceState(@NonNull Bundle outState) {
        super.onSaveInstanceState(outState);
        webView.saveState(outState);
    }

    private void checkPermissions() {
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.CAMERA) != PackageManager.PERMISSION_GRANTED) {
            ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.CAMERA}, PERMISSION_REQUEST_CODE);
        }
    }
}

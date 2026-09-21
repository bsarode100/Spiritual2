# SpiritualShaadi Proguard Rules
-keepattributes *Annotation*
-keepclassmembers class * {
    @android.webkit.JavascriptInterface <methods>;
}
-keep class androidx.swiperefreshlayout.widget.SwipeRefreshLayout { *; }
-dontwarn android.webkit.**

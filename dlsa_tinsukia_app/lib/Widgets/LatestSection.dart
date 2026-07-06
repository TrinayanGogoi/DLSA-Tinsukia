import 'package:flutter/material.dart';
import 'dart:convert';
import 'dart:io';
import 'package:http/http.dart' as http;
import 'dart:async';
import '../DisplayContent/DisplayContent.dart';

class LatestSection extends StatefulWidget {
  const LatestSection({super.key});

  @override
  _LatestSectionState createState() => _LatestSectionState();
}

class _LatestSectionState extends State<LatestSection> with TickerProviderStateMixin {
  List<Map<String, dynamic>> _latestItems = [];
  bool _isLoading = true;
  late final AnimationController _rainbowController;
  late final ScrollController _scrollController;
  final double rowHeight = 48.0;
  final double _scrollSpeed = 30; // pixels per second

  // Rainbow colors
  final List<Color> rainbowColors = [
    const Color(0xFFFF0000),
    const Color(0xFFFF9900),
    const Color(0xFFFFFF00),
    const Color(0xFF33CC33),
    const Color(0xFF00CCFF),
    const Color(0xFF3366FF),
    const Color(0xFFCC33FF),
  ];

  final String baseUrl = Platform.isAndroid
      ? 'http://10.0.2.2:8000'
      : 'http://localhost:8000';

  @override
  void initState() {
    super.initState();
    _rainbowController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 500),
    )..repeat();
    _scrollController = ScrollController();
    _fetchLatestItems();
  }

  @override
  void dispose() {
    _rainbowController.dispose();
    _scrollController.dispose();
    super.dispose();
  }

  Future<void> _fetchLatestItems() async {
    try {
      final response = await http.get(Uri.parse('$baseUrl/api/latest/Retrieve'));
      if (response.statusCode == 200) {
        final data = json.decode(response.body);
        if (data['success']) {
          final now = DateTime.now();
          final validItems = (data['data'] as List).where((item) {
            if (item['expires_at'] == null) return true;
            final expiryDate = DateTime.parse(item['expires_at']);
            return expiryDate.isAfter(now);
          }).toList();

          setState(() {
            _latestItems = List<Map<String, dynamic>>.from(validItems);
            _isLoading = false;
          });

          WidgetsBinding.instance.addPostFrameCallback((_) {
            _startAutoScroll();
          });
        }
      }
    } catch (e) {
      print('Error fetching latest items: $e');
      setState(() {
        _isLoading = false;
      });
    }
  }

  void _startAutoScroll() {
  const fps = 60;
  final pixelsPerTick = _scrollSpeed / fps;

  WidgetsBinding.instance.addPostFrameCallback((_) {
    final maxExtent = _scrollController.position.maxScrollExtent;
    _scrollController.jumpTo(0); // ✅ since we're reversed, 0 is bottom
  });

  Timer.periodic(const Duration(milliseconds: 1000 ~/ fps), (timer) {
    if (_scrollController.hasClients && _latestItems.isNotEmpty) {
      final maxExtent = _scrollController.position.maxScrollExtent;
      final currentOffset = _scrollController.offset;

      if (currentOffset >= maxExtent) {
        _scrollController.jumpTo(0); // ✅ reset to start (bottom)
      } else {
        _scrollController.jumpTo(currentOffset + pixelsPerTick); // ✅ scroll "up" visually
      }
    } else {
      timer.cancel();
    }
  });
}





  Widget _buildRainbowTag() {
    return AnimatedBuilder(
      animation: _rainbowController,
      builder: (context, child) {
        final baseIndex = ((_rainbowController.value * rainbowColors.length) % rainbowColors.length).floor();
        final progress = (_rainbowController.value * rainbowColors.length) % 1.0;
        final currentColor = rainbowColors[baseIndex];
        final nextColor = rainbowColors[(baseIndex + 1) % rainbowColors.length];

        final interpolatedColors = [
          Color.lerp(currentColor, nextColor, progress)!,
          Color.lerp(
            rainbowColors[(baseIndex + 1) % rainbowColors.length],
            rainbowColors[(baseIndex + 2) % rainbowColors.length],
            progress,
          )!,
        ];

        return Container(
          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 2),
          margin: const EdgeInsets.only(right: 10),
          decoration: BoxDecoration(
            gradient: LinearGradient(
              colors: interpolatedColors,
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.circular(10),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.08),
                blurRadius: 2,
                offset: const Offset(0, 1),
              ),
            ],
          ),
          child: const Text(
            'NEW',
            style: TextStyle(
              color: Colors.white,
              fontSize: 12,
              fontWeight: FontWeight.bold,
              letterSpacing: 1.1,
            ),
          ),
        );

      },
    );
  }

  // Widget _buildItem(Map<String, dynamic> item) {
  //   final upload = item['upload'] ?? {};
  //   return InkWell(
  //     onTap: () {
  //       Navigator.push(
  //         context,
  //         MaterialPageRoute(
  //           builder: (context) => DisplaycontentPage(
  //             achievement: upload,
  //             categoryName: 'Latest',
  //             currentIndex: _latestItems.indexOf(item),
  //             itemsList: _latestItems.map<Map<String, dynamic>>((item) => Map<String, dynamic>.from(item['upload'] ?? {})).toList(),
  //           ),
  //         ),
  //       );
  //     },
  //     child: Container(
  //       height: rowHeight,
  //       padding: const EdgeInsets.symmetric(horizontal: 18),
  //       alignment: Alignment.centerLeft,
  //       child: Row(
  //         children: [
  //           _buildRainbowTag(),
  //           Expanded(
  //             child: Text(
  //               upload['title'] ?? '',
  //               style: const TextStyle(
  //                 fontSize: 16,
  //                 fontWeight: FontWeight.w600,
  //                 color: Color(0xFF1A237E),
  //               ),
  //               overflow: TextOverflow.ellipsis,
  //               maxLines: 2,
  //             ),
  //           ),
  //         ],
  //       ),
  //     ),
  //   );
  // }

  Widget _buildItem(Map<String, dynamic> item) {
    final upload = item['upload'] ?? {};
    final title = upload['title'] ?? '';

    return InkWell(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (context) => DisplaycontentPage(
              achievement: upload,
              categoryName: 'Latest',
              currentIndex: _latestItems.indexOf(item),
              itemsList: _latestItems
                  .map<Map<String, dynamic>>((item) => Map<String, dynamic>.from(item['upload'] ?? {}))
                  .toList(),
            ),
          ),
        );
      },
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 6),
        margin: const EdgeInsets.symmetric(vertical: 6),
        alignment: Alignment.centerLeft,
        child: RichText(
          text: TextSpan(
            children: [
              const TextSpan(
                text: '• ',
                style: TextStyle(
                  fontSize: 20,
                  color: Colors.grey,
                ),
              ),
              TextSpan(
                text: upload['title'] ?? '',
                style: const TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w600,
                  color: Color(0xFF1A237E),
                ),
              ),
              const WidgetSpan(
                child: SizedBox(width: 8),
              ),
              WidgetSpan(
                alignment: PlaceholderAlignment.middle,
                child: _buildRainbowTag(),
              ),
            ],
          ),
          maxLines: 2,
          overflow: TextOverflow.ellipsis,
        ),
      ),


    );
  }



  @override
  Widget build(BuildContext context) {
    List<Widget> buildDuplicatedItems() { // duplicating the list
      const spacer = SizedBox(height: 200); // Or use Divider()
      List<Widget> widgets = [];

      for (int i = 0; i < 3; i++) {
        for (var item in _latestItems) {
          widgets.add(_buildItem(item));
        }
        widgets.add(spacer);
      }

      return widgets.reversed.toList(); // Reverse for bottom-to-top
    }




    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Latest Updates',
          style: TextStyle(
            fontSize: 24,
            fontWeight: FontWeight.bold,
            color: Color.fromARGB(255, 122, 120, 120),
          ),
        ),
        const SizedBox(height: 24),
        Container(
          height: rowHeight * 4,
          clipBehavior: Clip.hardEdge,
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.08),
                blurRadius: 8,
                offset: const Offset(0, 2),
              ),
            ],
          ),
          child: _isLoading
              ? const Center(child: CircularProgressIndicator())
              : _latestItems.isEmpty
                  ? const Center(
                      child: Text(
                        'No latest updates available',
                        style: TextStyle(fontSize: 16, color: Colors.grey),
                      ),
                    )
                  : ListView(
                    controller: _scrollController,
                    reverse: false, // ✅ scroll bottom to top
                    physics: const NeverScrollableScrollPhysics(),
                    padding: EdgeInsets.zero,
                    children: buildDuplicatedItems(),
                  )


        ),
      ],
    );
  }
}

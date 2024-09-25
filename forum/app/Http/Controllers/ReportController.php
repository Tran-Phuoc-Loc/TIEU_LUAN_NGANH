<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    // Hiển thị tất cả các báo cáo
    public function index()
    {
        $reports = Report::with('post', 'user')->paginate(10);
        return view('admin.reports.index', compact('reports'));
    }

    // Hiển thị chi tiết một báo cáo
    public function show($id)
    {
        /** @var Report $report */
        $report = Report::with('post', 'user')->findOrFail($id);
        return view('admin.reports.show', compact('report'));
    }

    // Xử lý báo cáo (duyệt hoặc từ chối)
    public function process(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        // Tùy chọn xử lý báo cáo
        if ($request->action == 'approve') {
            // Ví dụ: xóa bài viết nếu báo cáo được duyệt
            $report->post->delete();
            return redirect()->route('admin.reports.index')->with('success', 'Báo cáo đã được xử lý và bài viết đã bị xóa.');
        } elseif ($request->action == 'reject') {
            // Từ chối báo cáo
            $report->delete();
            return redirect()->route('admin.reports.index')->with('success', 'Báo cáo đã bị từ chối.');
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id',
            'reason' => 'required|string|max:255',
        ]);

        Report::create([
            'post_id' => $request->post_id,
            'user_id' => Auth::id(),
            'reason' => $request->reason,
        ]);

        return back()->with('success', 'Bài viết đã được báo cáo.');
    }
}

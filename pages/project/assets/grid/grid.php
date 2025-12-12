<?php
/**
 * Project Grid Section Component
 * Supports multiple tool logos per project
 */
?>

<!-- Projects Grid -->
<section class="section">
    <div class="container">
        <?php if (!empty($projects)): ?>
            <div class="grid grid-3">
                <?php foreach ($projects as $project): ?>
                    <div class="project-card">
                        <div class="project-image">
                            <?php if (!empty($project['project_image'])): ?>
                                <img src="/portofolio/assets/images/<?php echo htmlspecialchars($project['project_image']); ?>" alt="Project">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,0.5);">
                                    Project Image
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($project['tool_logo'])): ?>
                                <?php 
                                // Parse multiple logos (comma-separated)
                                $logos = explode(',', $project['tool_logo']);
                                $logos = array_filter(array_map('trim', $logos));
                                ?>
                                <div class="project-tools">
                                    <?php foreach ($logos as $logo): ?>
                                        <img src="/portofolio/assets/images/<?php echo htmlspecialchars($logo); ?>" alt="Tool">
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="project-content">
                            <h4>Project #<?php echo $project['id']; ?></h4>
                            <p class="card-text">
                                <?php 
                                $description = $project['description'] ?? '';
                                echo htmlspecialchars(strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description);
                                ?>
                            </p>
                            
                            <?php if (!empty($project['project_link_github'])): ?>
                                <a href="<?php echo htmlspecialchars($project['project_link_github']); ?>" target="_blank" class="project-link">
                                    View on GitHub &#8599;
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div style="font-size: 4rem; margin-bottom: 1rem;">💻</div>
                <h3>No Projects Yet</h3>
                <p>Project portfolio will be displayed here once added to the database.</p>
            </div>
        <?php endif; ?>
    </div>
</section>
